<?php
require_once __DIR__ . '/../config/database.php';

class Utils {
    
    // Format currency in VND
    public static function formatCurrency($amount, $currency = 'VND') {
        if ($currency == 'VND') {
            return number_format($amount, 0, ',', '.') . ' ₫';
        }
        return number_format($amount, 2, '.', ',') . ' ' . $currency;
    }
    
    // Format date
    public static function formatDate($date, $format = 'd/m/Y') {
        if (empty($date)) return '';
        return date($format, strtotime($date));
    }
    
    // Generate contract number
    public static function generateContractNumber($type, $year, $archiveNumber = null) {
        $db = new Database();
        $conn = $db->connect();
        
        $prefix = '';
        switch($type) {
            case 'purchase_contract':
                $prefix = 'PC';
                break;
            case 'sales_contract':
                $prefix = 'SC';
                break;
            case 'purchase_order':
                $prefix = 'PO';
                break;
            case 'sales_order':
                $prefix = 'SO';
                break;
        }
        
        // Check if sequence exists
        $sql = "SELECT last_number FROM numbering_sequences 
                WHERE sequence_type = :type AND year = :year 
                AND (archive_number = :archive OR (archive_number IS NULL AND :archive IS NULL))";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type' => $type,
            ':year' => $year,
            ':archive' => $archiveNumber
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Update existing sequence
            $newNumber = $result['last_number'] + 1;
            $updateSql = "UPDATE numbering_sequences 
                         SET last_number = :number 
                         WHERE sequence_type = :type AND year = :year 
                         AND (archive_number = :archive OR (archive_number IS NULL AND :archive IS NULL))";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([
                ':number' => $newNumber,
                ':type' => $type,
                ':year' => $year,
                ':archive' => $archiveNumber
            ]);
        } else {
            // Create new sequence
            $newNumber = 1;
            $insertSql = "INSERT INTO numbering_sequences (sequence_type, year, archive_number, last_number, prefix) 
                         VALUES (:type, :year, :archive, :number, :prefix)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->execute([
                ':type' => $type,
                ':year' => $year,
                ':archive' => $archiveNumber,
                ':number' => $newNumber,
                ':prefix' => $prefix
            ]);
        }
        
        // Format: PREFIX-YYYY-ARCHIVE-NNNN (e.g., PC-2024-A1-0001)
        $formattedNumber = sprintf("%s-%04d", $prefix, $newNumber);
        if ($archiveNumber) {
            $formattedNumber = sprintf("%s-%s-%04d", $prefix, $archiveNumber, $newNumber);
        }
        $formattedNumber = sprintf("%s-%d-%s", $prefix, $year, 
            $archiveNumber ? sprintf("%s-%04d", $archiveNumber, $newNumber) : sprintf("%04d", $newNumber));
        
        return $formattedNumber;
    }
    
    // Create folder for contract documents
    public static function createContractFolder($year, $customerOrSupplierName, $contractNumber) {
        $basePath = UPLOAD_DIR;
        $yearPath = $basePath . $year . '/';
        $clientPath = $yearPath . self::sanitizeFolderName($customerOrSupplierName) . '/';
        $contractPath = $clientPath . self::sanitizeFolderName($contractNumber) . '/';
        
        if (!file_exists($yearPath)) {
            mkdir($yearPath, 0755, true);
        }
        if (!file_exists($clientPath)) {
            mkdir($clientPath, 0755, true);
        }
        if (!file_exists($contractPath)) {
            mkdir($contractPath, 0755, true);
        }
        
        return $contractPath;
    }
    
    // Sanitize folder name
    private static function sanitizeFolderName($name) {
        // Remove special characters and spaces
        $name = preg_replace('/[^A-Za-z0-9\-_]/', '_', $name);
        return $name;
    }
    
    // Upload file
    public static function uploadFile($file, $targetPath) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error'];
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File too large'];
        }
        
        $fileName = basename($file['name']);
        $targetFile = $targetPath . $fileName;
        
        // Check if file already exists, add number suffix
        $counter = 1;
        while (file_exists($targetFile)) {
            $pathInfo = pathinfo($fileName);
            $targetFile = $targetPath . $pathInfo['filename'] . '_' . $counter . '.' . $pathInfo['extension'];
            $counter++;
        }
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return ['success' => true, 'path' => $targetFile, 'filename' => basename($targetFile)];
        }
        
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
    
    // Sanitize input
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
        } else {
            $data = htmlspecialchars(strip_tags(trim($data)));
        }
        return $data;
    }
    
    // Get status badge HTML
    public static function getStatusBadge($status) {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Nháp</span>',
            'active' => '<span class="badge bg-success">Đang hiệu lực</span>',
            'completed' => '<span class="badge bg-primary">Hoàn thành</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
            'submitted' => '<span class="badge bg-info">Đã gửi</span>',
            'approved' => '<span class="badge bg-success">Đã duyệt</span>',
        ];
        return $badges[$status] ?? '<span class="badge bg-light">' . $status . '</span>';
    }
}
?>
