<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sauvegarde complète de la base MySQL en SQL compressé (PHP pur :
 * ne dépend pas de mysqldump, donc fonctionne aussi sur hébergement mutualisé).
 * Fichiers : storage/app/backups/db_AAAAMMJJ_HHMMSS_<raison>.sql.gz
 */
class BackupService
{
    public const DAYS = 30;          // les sauvegardes plus anciennes sont supprimées
    private const CHUNK = 500;

    public static function dir(): string
    {
        $d = storage_path('app/backups');
        if (!is_dir($d)) {
            @mkdir($d, 0775, true);
        }
        return $d;
    }

    /** @return array{ok:bool,file:?string,size:int,error:?string} */
    public static function run(string $reason = 'manual'): array
    {
        $file = null;
        try {
            @set_time_limit(0);
            @ini_set('memory_limit', '512M');

            $reason = preg_replace('/[^a-z0-9_-]/i', '', $reason) ?: 'manual';
            $name   = 'db_' . date('Ymd_His') . '_' . $reason . '.sql.gz';
            $file   = self::dir() . DIRECTORY_SEPARATOR . $name;
            $tmp    = $file . '.part';

            $gz = gzopen($tmp, 'wb6');
            if (!$gz) {
                throw new \RuntimeException('Impossible d\'écrire dans ' . self::dir());
            }

            $pdo  = DB::connection()->getPdo();
            $base = DB::connection()->getDatabaseName();

            gzwrite($gz, "-- Sauvegarde {$base} le " . date('Y-m-d H:i:s') . " ({$reason})\n");
            gzwrite($gz, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            foreach ($tables as $row) {
                $table = array_values((array) $row)[0];
                $q     = '`' . str_replace('`', '``', $table) . '`';

                $create = (array) DB::selectOne("SHOW CREATE TABLE {$q}");
                gzwrite($gz, "DROP TABLE IF EXISTS {$q};\n" . array_values($create)[1] . ";\n\n");

                $batch = [];
                $cols  = null;
                foreach (DB::table($table)->cursor() as $r) {
                    $r = (array) $r;
                    if ($cols === null) {
                        $cols = '(' . implode(',', array_map(fn ($c) => '`' . str_replace('`', '``', $c) . '`', array_keys($r))) . ')';
                    }
                    $vals = [];
                    foreach ($r as $v) {
                        $vals[] = $v === null ? 'NULL' : (is_int($v) || is_float($v) ? (string) $v : $pdo->quote((string) $v));
                    }
                    $batch[] = '(' . implode(',', $vals) . ')';
                    if (count($batch) >= self::CHUNK) {
                        gzwrite($gz, "INSERT INTO {$q} {$cols} VALUES\n" . implode(",\n", $batch) . ";\n");
                        $batch = [];
                    }
                }
                if ($batch) {
                    gzwrite($gz, "INSERT INTO {$q} {$cols} VALUES\n" . implode(",\n", $batch) . ";\n");
                }
                gzwrite($gz, "\n");
            }

            gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n-- FIN\n");
            gzclose($gz);
            rename($tmp, $file);

            self::prune();
            $size = (int) @filesize($file);
            AuditService::record('backup', ['new_values' => ['file' => $name, 'size' => $size, 'reason' => $reason]]);
            return ['ok' => true, 'file' => $name, 'size' => $size, 'error' => null];
        } catch (\Throwable $e) {
            if ($file) { @unlink($file . '.part'); }
            Log::error('Backup: ' . $e->getMessage());
            AuditService::record('backup_failed', ['new_values' => ['error' => $e->getMessage(), 'reason' => $reason]]);
            return ['ok' => false, 'file' => null, 'size' => 0, 'error' => $e->getMessage()];
        }
    }

    public static function list(): array
    {
        $out = [];
        foreach (glob(self::dir() . DIRECTORY_SEPARATOR . 'db_*.sql.gz') ?: [] as $f) {
            $out[] = ['name' => basename($f), 'size' => filesize($f), 'time' => filemtime($f)];
        }
        usort($out, fn ($a, $b) => $b['time'] <=> $a['time']);
        return $out;
    }

    private static function prune(): void
    {
        $limit = time() - self::DAYS * 86400;
        $all = self::list();          // du plus récent au plus ancien : la plus récente est toujours gardée
        foreach (array_slice($all, 1) as $old) {
            if ($old['time'] < $limit) {
                @unlink(self::dir() . DIRECTORY_SEPARATOR . $old['name']);
            }
        }
    }
}
