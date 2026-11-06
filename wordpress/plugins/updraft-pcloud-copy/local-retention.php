<?php
namespace Updraft_PCloud_Copy;

defined('ABSPATH') || exit;

function local_expiry_candidate(array $history, array $entities, string $dir, int $cutoff): array {
    krsort($history, SORT_NUMERIC);
    $newest_complete = [];
    $seen = [];
    foreach ($history as $timestamp => $entry) {
        $nonce = $entry['nonce'] ?? null;
        if (!valid_backup_id($nonce) || isset($seen[$nonce]) || !empty($entry['meta_foreign']) || count($entry['incremental_sets'] ?? []) > 1) {
            continue;
        }

        $seen[$nonce] = true;
        $files = [];
        $complete = true;
        $database = !empty($entry['db']);
        $other = false;
        foreach ($entities as $entity) {
            foreach ((array) ($entry[$entity] ?? []) as $name) {
                if (!valid_archive_name($name, $nonce)) {
                    continue 3;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $name;
                clearstatcache(true, $path);
                if (is_link($path) || (file_exists($path) && (!is_file($path) || realpath($path) !== $path))) {
                    continue 3;
                }
                $other = $other || 'db' !== $entity;
                if (is_file($path) && filesize($path) > 0) {
                    $files[$name] = $path;
                } else {
                    $complete = false;
                }
            }
        }
        if (!$files) {
            continue;
        }

        $type = $database ? ($other ? 'full' : 'database') : 'files';
        if (isset($newest_complete[$type]) && (int) $timestamp <= $cutoff && empty($entry['always_keep'])) {
            return $files;
        }
        if ($complete) {
            $newest_complete[$type] = true;
        }
    }
    return [];
}

function prune_local_backups(int $days, \DateTimeImmutable $now, array $state): void {
    if ($days < 1 || ($state['local_retention_date'] ?? '') === $now->format('Y-m-d') ||
        !empty($state['job']) || !empty($state['recovery']) || get_site_option('updraft_restore_in_progress')) {
        return;
    }
    global $updraftplus;
    try {
        if (!is_object($updraftplus) || !class_exists('UpdraftPlus_Backup_History')) {
            throw new \RuntimeException('UpdraftPlus is niet beschikbaar voor de lokale opruiming.');
        }
        $dir = backup_directory();
        $entities = array_keys($updraftplus->get_backupable_file_entities(true, true));
        $entities = array_unique(array_merge($entities, ['db']));
        $files = local_expiry_candidate(\UpdraftPlus_Backup_History::get_history(), $entities, $dir,
            $now->modify("-$days days")->getTimestamp());
        foreach ($files as $path) {
            clearstatcache(true, $path);
            if (is_link($path) || realpath($path) !== $path || !is_file($path) || !@unlink($path)) {
                throw new \RuntimeException('Een verlopen lokaal archiefdeel kon niet veilig worden verwijderd.');
            }
        }
        if ($files) {
            \UpdraftPlus_Backup_History::rebuild(false);
        } else {
            $state['local_retention_date'] = $now->format('Y-m-d');
        }
        unset($state['local_retention_error']);
    } catch (\RuntimeException $error) {
        $state['local_retention_error'] = $error->getMessage();
        $state['local_retention_date'] = $now->format('Y-m-d');
    }
    update_option(STATE, $state, false);
}
