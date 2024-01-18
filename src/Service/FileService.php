<?php

namespace App\Service;

class FileService
{
    public function getTreeInfo($dir, $currentLevel = 0)
    {


        $info = $this->getInfo($dir);



        $subDirs = array_diff(scandir($dir), ['..', '.']);
        foreach ($subDirs as $subDir) {
            $subDirPath = $dir . '/' . $subDir;
            if (is_dir($subDirPath)) {
                $subInfo = $this->getTreeInfo($subDirPath, $currentLevel + 1);
                if ($subInfo) {
                    $info['folders'] += $subInfo['folders'];
                    $info['files'] += $subInfo['files'];
                    $info['links'] += $subInfo['links'];
                    $info['totalSize'] += $subInfo['totalSize'];
                    $info['children'][] = $subInfo;

                }
            }
        }

        return $info;

    }


    private function getInfo($dir)
    {
        $folders = 0;
        $files = 0;
        $links = 0;
        $totalSize = 0;

        $contents = array_diff(scandir($dir), ['..', '.']);
        foreach ($contents as $content) {
            $path = $dir . '/' . $content;

            if (is_dir($path)) {
                $folders++;
            } elseif (is_file($path)) {
                $files++;
                $totalSize += filesize($path);
            } elseif (is_link($path)) {
                $links++;
            }
        }

        $info = [
            'name' => basename($dir),
            'folders' => $folders,
            'files' => $files,
            'links' => $links,
            'totalSize' => $totalSize,
            'children' => [],
        ];

        return $info;
    }

}


