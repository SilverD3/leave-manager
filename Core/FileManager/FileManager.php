<?php

namespace Core\FileManager;

use \Exception;

class FileManager
{
    /**
     * Stores file in the specified directory
     * 
     * @param array $file Uploaded file with props: name, size, error and tmp_name
     * @param FileValidation $validations Validation constraints
     * @param string $directoryPath Directory in which the file will be stored
     * 
     * @throws Exception When unable to store the file or when the file contains errors
     * @return UploadStatus The status of the upload
     */
    public function saveFile(array $file, FileValidation $validations, string $directoryPath): UploadStatus
    {
        $uploadStatus = new UploadStatus();
        $uploadStatus->setErrors([]);

        try {
            $this->validateFile($file, $validations);

            $fileNameAndExtension = $this->getFilenameAndExtension(
                $this->getFileType($this->getFileExtension($file['name']))->name,
                $file['name']
            );

            $fileName = $fileNameAndExtension[0] . "." . $fileNameAndExtension[1];
            $directory = $this->createOrGetDirectory($directoryPath);
            $destination = $directory . (str_ends_with($directory, "/") ? "" : DS) . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception("Echec d'enregistrement du fichier.");
            }

            $uploadStatus->setSucceeded(true);
            $uploadStatus->setFilename($fileName);

            return $uploadStatus;
        } catch (Exception $e) {
            $uploadStatus->setErrors([$e->getMessage()]);
            $uploadStatus->setSucceeded(false);

            return $uploadStatus;
        }
    }

    /**
     * Resolves file full path
     * @param string $directory Directory name
     * @param string $filename File nmae
     * 
     * @return string The full path to the file
     */
    public function getFullPath(string $directory, string $filename): string
    {
        return $directory . (str_ends_with($directory, "/") ? "" : DS) . $filename;
    }

    /**
     * Delete file from the disk storage
     * 
     * @param string $filename File name
     * @param string $directoryPath Directory path
     * 
     * @return void
     */
    public function deleteFile(string $filename, string $directoryPath): void
    {
        $filePath = $this->getFullPath($directoryPath, $filename);
        if (!file_exists($filePath)) {
            throw new Exception(sprintf("Le fichier %s n'existe pas", $filePath));
        }

        if (!unlink($filePath)) {
            throw new Exception(sprintf("Impossible de supprimer le fichier %s", $filePath));
        }
    }

    /**
     * Deletes a directory
     * 
     * @param string $directoryPath Directory path
     * @param bool $recursively Whether to perform a recursive deletion or not
     * 
     * @return void
     */
    public function deleteDirectory(string $directoryPath, bool $recursively): void
    {
        if ($recursively) {
            try {
                $iterator = new \DirectoryIterator($directoryPath);
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isDot()) continue;
                    if ($fileinfo->isDir()) {
                        if ($this->deleteDirectory($fileinfo->getPathname(), true))
                            @rmdir($fileinfo->getPathname());
                    }
                    if ($fileinfo->isFile()) {
                        @unlink($fileinfo->getPathname());
                    }
                }
            } catch (Exception $e) {
                throw new Exception(sprintf("Impossible de supprimer le repertoire %s, Cause: %s", $directoryPath, $e->getMessage()));
            }
        } else {
            if ($this->directoryHasSubdirectories($directoryPath)) {
                throw new Exception(sprintf("Impossible de supprimer le repertoire %s parce qu'il contient des sous-pertoires", $directoryPath));
            }

            if (is_dir($directoryPath)) {
                rmdir($directoryPath);
            } else {
                unlink($directoryPath);
            }
        }
    }

    /**
     * Checks if a directory contains subdirectories
     * 
     * @param mixed $dirPath Directory path
     * 
     * @return bool Returns true if the directory has subdirectories, false otherwise
     */
    public function directoryHasSubdirectories($dirPath): bool
    {
        if (!is_dir($dirPath)) {
            return false;
        }

        $handle = opendir($dirPath);
        if ($handle === false) {
            return false;
        }

        $hasSubdirs = false;
        while (($entry = readdir($handle)) !== false) {
            if ($entry !== '.' && $entry !== '..') {
                $hasSubdirs = true;
                break;
            }
        }

        closedir($handle);
        return $hasSubdirs;
    }

    /**
     * Resolves file name and file extension
     * 
     * @param string $fileType The file type. @see App\FileManager\FileType
     * @param string $fileName The original file name
     * 
     * @return array Returns array containing the file name and extension in this order
     */
    public function getFilenameAndExtension(string $fileType, string $fileName): array
    {
        if (empty($fileName)) {
            throw new Exception('Le nom du fichier ne doit pas être vide');
        }

        $fileInfo = pathinfo($fileName);

        if (!isset($fileInfo['extension'])) {
            return [strtoupper($fileType . md5($fileName)), ""];
        } else {
            return [strtoupper($fileType . md5($fileInfo['basename'])), $fileInfo['extension']];
        }
    }

    /**
     * Resolves file type
     * 
     * @param string $extension File extension
     * 
     * @return FileType Returns the corresponding file type
     */
    public function getFileType(string $extension): FileType
    {
        switch ($extension) {
            case "jpeg":
            case "jpg":
            case "png":
            case "gif":
                return FileType::IMAGE;
            case "xls":
            case "xlsb":
            case "xlsm":
            case "xlsx":
                return FileType::EXCEL;
            case "doc":
            case "docx":
            case "docm":
                return FileType::WORD;
            case "pdf":
                return FileType::PDF;
            case "zip":
            case "iso":
            case "tar":
            case "br":
            case "bz2":
            case "gz":
            case "xz":
            case "7z":
            case "rar":
            case "zipx":
                return FileType::ARCHIVE;
            case "mp3":
            case "ogg":
            case "wav":
            case "aac":
                return FileType::AUDIO;
            case "mp4":
            case "avi":
            case "webm":
            case "mkv":
            case "mpeg":
            case "mpg":
            case "m4v":
            case "m4p":
            case "wmv":
                return FileType::VIDEO;
            default:
                return FileType::UNKNOWN;
        };
    }

    /**
     * Get file extensions from file type
     * @param FileType $fileType File type
     * 
     * @return string Returns the corresponding file extensions 
     * or an empty string if no match found
     */
    public function getFileTypeExtensions(FileType $fileType): string
    {
        switch ($fileType) {
            case FileType::IMAGE:
                return "jpeg, jpg, png, gif";
            case FileType::EXCEL:
                return "xls, xlsb, xlsm, xlsx";
            case FileType::WORD:
                return "doc, docx, docm";
            case FileType::PDF:
                return "pdf";
            case FileType::ARCHIVE:
                return "zip, iso, tar, br, bz2, gz, xz, 7z, rar, zipx";
            case FileType::AUDIO:
                return "mp3, ogg, wav, aac";
            case FileType::VIDEO:
                return "mp4, avi, webm, mkv, mpeg, mpg, m4v, m4p, wmv";
            case FileType::UNKNOWN:
                return "";
        };

        return "";
    }

    /**
     * Resolves file extension
     * 
     * @param string $fileName Original file name
     * 
     * @return string Returns the file extension or an empty string
     */
    public function getFileExtension(string $fileName): string
    {
        $fileInfo = pathinfo($fileName);
        return isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
    }

    /**
     * Validates file provided
     * 
     * @param array $file The upload file
     * @param FileValidation $validation Validation constraints
     * 
     * @return void
     */
    public function validateFile(array $file, FileValidation $validation): void
    {
        if ($file['error'] !== 0) {
            throw new Exception("Le fichier n'a pas pu être téléchargé. Veuillez réessayer !");
        }

        if ($validation->getMinSize() != null && intval($file['size']) < $validation->getMinSize()) {
            throw new Exception(sprintf("La taille du fichier n'est pas correcte. %s", $this->constraintInfo($validation)));
        }

        if ($validation->getMaxSize() != null && intval($file['size']) > $validation->getMaxSize()) {
            throw new Exception(sprintf("La taille maximale autorisée est dépassée. %s", $this->constraintInfo($validation)));
        }

        if ($validation->getFileTypes() != null && count($validation->getFileTypes()) > 0) {
            $extension = $this->getFileExtension($file['name']);
            if (!in_array($this->getFileType($extension), $validation->getFileTypes())) {
                throw new Exception(sprintf("Le format du fichier est invalide. %s", $this->constraintInfo($validation)));
            }
        }
    }

    /**
     * Resolves file validation constraints into a single string
     * 
     * @param Filevalidation $validation Validation constraints
     * 
     * @return string Returns the file constraints
     */
    private function constraintInfo(Filevalidation $validation): string
    {
        $constraintInfo = [];

        if ($validation->getMinSize() != null) {
            $constraintInfo[] = "Taille mininale: " . $this->parseFileSize($validation->getMinSize());
        }

        if ($validation->getMaxSize() != null) {
            $constraintInfo[] = "Taille maximale: " . $this->parseFileSize($validation->getMaxSize());
        }

        if ($validation->getFileTypes() != null && count($validation->getFileTypes()) > 0) {
            $allowedExtensions = [];
            foreach ($validation->getFileTypes() as $fileType) {
                $allowedExtensions[] = $this->getFileTypeExtensions($fileType);
            }

            $constraintInfo[] = sprintf("Formats acceptés: %s", implode(", ", $allowedExtensions));
        }

        return count($constraintInfo) > 0 ? implode("; ", $constraintInfo) : '';
    }

    /**
     * Creates directory if it not exists
     * 
     * @param string $directoryPath Directory path
     * 
     * @return string Returns the directory path
     */
    public function createOrGetDirectory(string $directoryPath): string
    {
        if (empty($directoryPath)) {
            throw new Exception("Nom de repertoire vide");
        }

        if (!is_dir($directoryPath)) {
            if (!mkdir($directoryPath, 0777, true)) {
                throw new Exception(sprintf("Impossible de créer le répertoire %s", $directoryPath));
            }
        }

        return $directoryPath;
    }

    /**
     * Parse file size into user-friendly string
     * 
     * @param int $size File size in bytes
     * 
     * @return string The parsed file size
     */
    public function parseFileSize(int $size): string
    {
        if ($size >= 1024 * 1023 * 1000) {
            $gigaSize = round($size / (1024 * 1023 * 1000), 2, PHP_ROUND_HALF_EVEN);
            return  $gigaSize . "Go";
        }

        if ($size >= 1024 * 1024) {
            $gigaSize = round($size / (1024 * 1024), 2, PHP_ROUND_HALF_EVEN);
            return  $gigaSize . "Mo";
        }

        if ($size >= 1024) {
            $gigaSize = round($size / 1024, 2, PHP_ROUND_HALF_EVEN);
            return  $gigaSize . "Ko";
        }

        return $size  . "O";
    }
}
