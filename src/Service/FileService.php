<?php

namespace App\Service;

class FileService
{
    public function getTreeInfo($dir, $levels, $currentLevel = 0)
    {
        if ($currentLevel > $levels) {
            return [];
        }

        $info = [
            'name' => basename($dir),
            'folders' => 0,
            'files' => 0,
            'links' => 0,
            'totalSize' => 0,
        ];

        $files = array_diff(scandir($dir), ['..', '.']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $childInfo = $this->getTreeInfo($path, $levels, $currentLevel + 1);
                if ($childInfo) {
                    $info['folders']++;
                    $info['totalSize'] += $childInfo['totalSize'];
                    $info['links'] += $childInfo['links'];
                    $info[$childInfo['name']] = $childInfo;
                }
            } elseif (is_file($path)) {
                $info['files']++;
                $info['totalSize'] += filesize($path);
            } elseif (is_link($path)) {
                $info['links']++;
            }
        }

        return $info;
    }

}
