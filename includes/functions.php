<?php
require_once 'config.php';

class Functions {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    // Get event statistics
    public function getEventStats($eventId) {
        $stats = [];
        
        // Contributions stats
        $stmt = $this->conn->prepare("
            SELECT 
                SUM(promised_amount) as total_promised,
                SUM(paid_amount) as total_paid,
                COUNT(*) as total_contributors,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as completed
            FROM contributions 
            WHERE event_id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $stats['contributions'] = $stmt->get_result()->fetch_assoc();
        
        // Invitations stats
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_invites,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'disapproved' THEN 1 ELSE 0 END) as declined
            FROM invitations 
            WHERE event_id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $stats['invitations'] = $stmt->get_result()->fetch_assoc();
        
        // Attendance stats
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_attendance,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as attended
            FROM attendance 
            WHERE event_id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $stats['attendance'] = $stmt->get_result()->fetch_assoc();
        
        return $stats;
    }
    
    // Get admin dashboard stats
    public function getAdminStats() {
        $stats = [];
        
        // Total events
        $result = $this->conn->query("SELECT COUNT(*) as total FROM events");
        $stats['total_events'] = $result->fetch_assoc()['total'];
        
        // Total event owners
        $result = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'event_owner' AND is_active = 1");
        $stats['total_owners'] = $result->fetch_assoc()['total'];
        
        // Today's events
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM events WHERE event_date = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $stats['today_events'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Total contributions
        $result = $this->conn->query("
            SELECT 
                SUM(promised_amount) as total_promised,
                SUM(paid_amount) as total_paid
            FROM contributions
        ");
        $contributions = $result->fetch_assoc();
        $stats['total_promised'] = $contributions['total_promised'] ?? 0;
        $stats['total_paid'] = $contributions['total_paid'] ?? 0;
        
        // Total posts
        $result = $this->conn->query("SELECT COUNT(*) as total FROM event_posts");
        $stats['total_posts'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }
    
    // Generate PDF report
    public function generatePDFReport($type, $eventId = null) {
        // TCPDF is already loaded via composer autoload
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('SmarboPlusEvent');
        $pdf->SetAuthor('SmarboPlusEvent System');
        $pdf->SetTitle('SmarboPlusEvent Report');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 20);
        
        // Title
        $pdf->Cell(0, 10, 'SmarboPlusEvent Report', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Report info
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Generated on: ' . date('F j, Y H:i:s'), 0, 1);
        $pdf->Ln(10);
        
        if ($type === 'admin') {
            $this->generateAdminReport($pdf);
        } elseif ($type === 'event' && $eventId) {
            $this->generateEventReport($pdf, $eventId);
        }
        
        return $pdf;
    }
    
    private function generateAdminReport($pdf) {
        // Get admin data
        $stats = $this->getAdminStats();
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'System Overview', 0, 1);
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(100, 7, 'Total Events:', 0, 0);
        $pdf->Cell(0, 7, $stats['total_events'], 0, 1);
        
        $pdf->Cell(100, 7, 'Total Event Owners:', 0, 0);
        $pdf->Cell(0, 7, $stats['total_owners'], 0, 1);
        
        $pdf->Cell(100, 7, "Today's Events:", 0, 0);
        $pdf->Cell(0, 7, $stats['today_events'], 0, 1);
        
        $pdf->Cell(100, 7, 'Total Promised Amount:', 0, 0);
        $pdf->Cell(0, 7, 'TZS ' . number_format($stats['total_promised'], 2), 0, 1);
        
        $pdf->Cell(100, 7, 'Total Paid Amount:', 0, 0);
        $pdf->Cell(0, 7, 'TZS ' . number_format($stats['total_paid'], 2), 0, 1);
        
        $pdf->Cell(100, 7, 'Total Posts:', 0, 0);
        $pdf->Cell(0, 7, $stats['total_posts'], 0, 1);
        
        $pdf->Ln(10);
        
        // Get all events
        $events = $this->conn->query("
            SELECT e.*, u.full_name as owner_name 
            FROM events e 
            JOIN users u ON e.event_owner_id = u.id 
            ORDER BY e.event_date DESC
        ");
        
        if ($events->num_rows > 0) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'All Events', 0, 1);
            $pdf->Ln(5);
            
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(230, 82, 31);
            $pdf->SetTextColor(255);
            
            // Table headers
            $pdf->Cell(50, 7, 'Event Name', 1, 0, 'C', true);
            $pdf->Cell(40, 7, 'Type', 1, 0, 'C', true);
            $pdf->Cell(40, 7, 'Date', 1, 0, 'C', true);
            $pdf->Cell(60, 7, 'Location', 1, 0, 'C', true);
            $pdf->Cell(50, 7, 'Owner', 1, 1, 'C', true);
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(0);
            $fill = false;
            
            while($event = $events->fetch_assoc()) {
                $pdf->Cell(50, 6, $event['event_name'], 1, 0, 'L', $fill);
                $pdf->Cell(40, 6, ucfirst($event['event_type']), 1, 0, 'C', $fill);
                $pdf->Cell(40, 6, date('M d, Y', strtotime($event['event_date'])), 1, 0, 'C', $fill);
                $pdf->Cell(60, 6, $event['location'], 1, 0, 'L', $fill);
                $pdf->Cell(50, 6, $event['owner_name'], 1, 1, 'L', $fill);
                $fill = !$fill;
            }
        }
    }
    
    private function generateEventReport($pdf, $eventId) {
        $stmt = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $event = $stmt->get_result()->fetch_assoc();
        
        if ($event) {
            // Event Header Styling
            $pdf->SetFillColor(230, 82, 31); // Primary color
            $pdf->SetTextColor(255);
            $pdf->SetFont('helvetica', 'B', 22);
            $pdf->Cell(0, 20, strtoupper($event['event_name']), 0, 1, 'C', true);
            
            $pdf->SetTextColor(0);
            $pdf->Ln(10);

            // Event Basic Info Table
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(40, 10, ' Event Type', 0, 0, 'L', true);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(95, 10, ' ' . ucfirst($event['event_type']), 0, 0);
            
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(40, 10, ' Event Date', 0, 0, 'L', true);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, ' ' . date('F j, Y', strtotime($event['event_date'])), 0, 1);
            
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(40, 10, ' Location', 0, 0, 'L', true);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, ' ' . $event['location'], 0, 1);
            
            $pdf->Ln(10);
            
            // Get event statistics
            $stats = $this->getEventStats($eventId);
            
            // Stats Summary Cards
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Financial Summary', 0, 1);
            $pdf->Ln(2);
            
            $colWidth = 65;
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(230, 82, 31);
            $pdf->SetTextColor(255);
            $pdf->Cell($colWidth, 8, 'Total Promised', 1, 0, 'C', true);
            $pdf->Cell($colWidth, 8, 'Total Paid', 1, 0, 'C', true);
            $pdf->Cell($colWidth, 8, 'Remaining Balance', 1, 1, 'C', true);
            
            $pdf->SetTextColor(0);
            $pdf->SetFont('helvetica', 'B', 12);
            $balance = $stats['contributions']['total_promised'] - $stats['contributions']['total_paid'];
            $pdf->Cell($colWidth, 12, 'TZS ' . number_format($stats['contributions']['total_promised'], 0), 1, 0, 'C');
            $pdf->SetTextColor(0, 150, 0);
            $pdf->Cell($colWidth, 12, 'TZS ' . number_format($stats['contributions']['total_paid'], 0), 1, 0, 'C');
            $pdf->SetTextColor(200, 0, 0);
            $pdf->Cell($colWidth, 12, 'TZS ' . number_format($balance, 0), 1, 1, 'C');
            
            $pdf->SetTextColor(0);
            $pdf->Ln(10);
            
            // Detailed Contributions Table
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Detailed Contributions List', 0, 1);
            $pdf->Ln(2);
            
            $contribStmt = $this->conn->prepare("SELECT * FROM contributions WHERE event_id = ? ORDER BY contributor_name ASC");
            $contribStmt->bind_param("i", $eventId);
            $contribStmt->execute();
            $contributions = $contribStmt->get_result();

            if ($contributions->num_rows > 0) {
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(230, 82, 31);
                $pdf->SetTextColor(255);

                // Table headers
                $pdf->Cell(70, 8, ' Contributor Name', 1, 0, 'L', true);
                $pdf->Cell(45, 8, ' Phone Number', 1, 0, 'C', true);
                $pdf->Cell(40, 8, ' Promised', 1, 0, 'R', true);
                $pdf->Cell(40, 8, ' Paid', 1, 0, 'R', true);
                $pdf->Cell(40, 8, ' Balance', 1, 1, 'R', true);

                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetTextColor(0);
                $fill = false;

                while($c = $contributions->fetch_assoc()) {
                    $pdf->SetFillColor(249, 249, 249);
                    $cBalance = $c['promised_amount'] - $c['paid_amount'];

                    $pdf->Cell(70, 8, ' ' . $c['contributor_name'], 1, 0, 'L', $fill);
                    $pdf->Cell(45, 8, ' ' . ($c['phone_number'] ?: '-'), 1, 0, 'C', $fill);
                    $pdf->Cell(40, 8, ' ' . number_format($c['promised_amount'], 0) . ' ', 1, 0, 'R', $fill);
                    $pdf->Cell(40, 8, ' ' . number_format($c['paid_amount'], 0) . ' ', 1, 0, 'R', $fill);

                    if ($cBalance > 0) $pdf->SetTextColor(200, 0, 0);
                    else $pdf->SetTextColor(0, 150, 0);

                    $pdf->Cell(40, 8, ' ' . number_format($cBalance, 0) . ' ', 1, 1, 'R', $fill);
                    $pdf->SetTextColor(0);

                    $fill = !$fill;
                }
            } else {
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->Cell(0, 10, 'No contributions recorded for this event.', 0, 1);
            }
            
            $pdf->Ln(10);
            
            // Attendance Summary
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Guest & Attendance Summary', 0, 1);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(80, 8, 'Total Invited Guests:', 0, 0);
            $pdf->Cell(0, 8, $stats['invitations']['total_invites'], 0, 1);

            $pdf->Cell(80, 8, 'Confirmed Attendance (RSVP):', 0, 0);
            $pdf->Cell(0, 8, $stats['invitations']['confirmed'], 0, 1);
            
            $pdf->Cell(80, 8, 'Actual Attendance:', 0, 0);
            $pdf->Cell(0, 8, $stats['attendance']['attended'], 0, 1);
        }
    }
    
    // Get event posts with pagination
    public function getEventPosts($eventId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->conn->prepare("
            SELECT ep.*, u.full_name as posted_by_name, u.avatar as posted_by_avatar,
                   (SELECT COUNT(*) FROM event_post_likes WHERE post_id = ep.id) as likes_count,
                   (SELECT COUNT(*) FROM event_post_comments WHERE post_id = ep.id) as comments_count
            FROM event_posts ep
            JOIN users u ON ep.posted_by = u.id
            WHERE ep.event_id = ?
            ORDER BY ep.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $eventId, $perPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while($post = $result->fetch_assoc()) {
            $post['media'] = $this->getPostMedia($post['id']);
            $posts[] = $post;
        }
        
        // Get total count for pagination
        $countStmt = $this->conn->prepare("SELECT COUNT(*) as total FROM event_posts WHERE event_id = ?");
        $countStmt->bind_param("i", $eventId);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
        
        return [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    // Get post media
    public function getPostMedia($postId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM event_post_media 
            WHERE post_id = ? 
            ORDER BY display_order, created_at
        ");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $media = [];
        while($item = $result->fetch_assoc()) {
            $media[] = $item;
        }
        return $media;
    }
    
    // Get previous and next posts
    public function getAdjacentPosts($postId, $eventId) {
        $adjacent = ['previous' => null, 'next' => null];
        
        // Previous post (older)
        $stmt = $this->conn->prepare("
            SELECT id, title FROM event_posts 
            WHERE event_id = ? AND id < ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bind_param("ii", $eventId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $adjacent['previous'] = $result->fetch_assoc();
        }
        
        // Next post (newer)
        $stmt = $this->conn->prepare("
            SELECT id, title FROM event_posts 
            WHERE event_id = ? AND id > ? 
            ORDER BY id ASC LIMIT 1
        ");
        $stmt->bind_param("ii", $eventId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $adjacent['next'] = $result->fetch_assoc();
        }
        
        return $adjacent;
    }
    
    // Create event post
    public function createEventPost($eventId, $title, $content, $postType, $userId, $media = []) {
        $this->conn->begin_transaction();
        
        try {
            // Insert post
            $stmt = $this->conn->prepare("
                INSERT INTO event_posts (event_id, title, content, post_type, posted_by) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isssi", $eventId, $title, $content, $postType, $userId);
            $stmt->execute();
            $postId = $stmt->insert_id;
            
            // Insert media if any
            if (!empty($media)) {
                foreach ($media as $index => $mediaItem) {
                    $mediaStmt = $this->conn->prepare("
                        INSERT INTO event_post_media (post_id, media_url, media_type, caption, display_order) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $mediaType = $this->getMediaType($mediaItem['url']);
                    $mediaStmt->bind_param("isssi", $postId, $mediaItem['url'], $mediaType, $mediaItem['caption'], $index);
                    $mediaStmt->execute();
                }
            }
            
            $this->conn->commit();
            return ['success' => true, 'post_id' => $postId];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Helper to detect media type
    private function getMediaType($url) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
        
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        }
        
        return 'image'; // Default
    }
    
    // Handle media upload
    public function handleMediaUpload($files) {
        $uploadDir = __DIR__ . '/../assets/uploads/posts/';
        $media = [];
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] === 0) {
                // Generate unique filename
                $originalName = basename($files['name'][$key]);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $originalName);
                $filePath = $uploadDir . $fileName;
                
                // Check file size (10MB max)
                if ($files['size'][$key] > 10 * 1024 * 1024) {
                    continue; // Skip files larger than 10MB
                }
                
                // Allowed file types
                $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $allowedVideoTypes = ['mp4', 'avi', 'mov', 'wmv'];
                
                if (in_array($extension, $allowedImageTypes) || in_array($extension, $allowedVideoTypes)) {
                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $mediaType = in_array($extension, $allowedImageTypes) ? 'image' : 'video';
                        $media[] = [
                            'url' => '/assets/uploads/posts/' . $fileName,
                            'media_type' => $mediaType,
                            'caption' => ''
                        ];
                    }
                }
            }
        }
        
        return $media;
    }
    
    // Get post details with media and author info
    public function getPostDetails($postId) {
        $stmt = $this->conn->prepare("
            SELECT ep.*, e.event_name, u.full_name as posted_by_name, u.avatar as posted_by_avatar,
                   (SELECT COUNT(*) FROM event_post_likes WHERE post_id = ep.id) as likes_count,
                   (SELECT COUNT(*) FROM event_post_comments WHERE post_id = ep.id) as comments_count
            FROM event_posts ep
            JOIN events e ON ep.event_id = e.id
            JOIN users u ON ep.posted_by = u.id
            WHERE ep.id = ?
        ");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $post = $result->fetch_assoc();
        $post['media'] = $this->getPostMedia($postId);
        
        return $post;
    }
    
    // Get post comments
    public function getPostComments($postId, $parentId = null) {
        if ($parentId === null) {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.full_name, u.avatar 
                FROM event_post_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? AND c.parent_id IS NULL
                ORDER BY c.created_at DESC
            ");
            $stmt->bind_param("i", $postId);
        } else {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.full_name, u.avatar 
                FROM event_post_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? AND c.parent_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->bind_param("ii", $postId, $parentId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comments = [];
        while($comment = $result->fetch_assoc()) {
            $comments[] = $comment;
        }
        
        return $comments;
    }
    
    // Check if user liked a post
    public function userLikedPost($postId, $userId) {
        $stmt = $this->conn->prepare("
            SELECT id FROM event_post_likes 
            WHERE post_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    // Toggle post like
    public function togglePostLike($postId, $userId) {
        if ($this->userLikedPost($postId, $userId)) {
            // Unlike
            $stmt = $this->conn->prepare("
                DELETE FROM event_post_likes 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->bind_param("ii", $postId, $userId);
            $stmt->execute();
            return ['success' => true, 'liked' => false];
        } else {
            // Like
            $stmt = $this->conn->prepare("
                INSERT INTO event_post_likes (post_id, user_id) 
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $postId, $userId);
            $stmt->execute();
            return ['success' => true, 'liked' => true];
        }
    }
    
    // Add comment to post
    public function addComment($postId, $userId, $comment, $parentId = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO event_post_comments (post_id, user_id, comment, parent_id) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iisi", $postId, $userId, $comment, $parentId);
        
        if ($stmt->execute()) {
            $commentId = $stmt->insert_id;
            return ['success' => true, 'comment_id' => $commentId];
        } else {
            return ['success' => false, 'message' => $stmt->error];
        }
    }
    
    // Get recent posts for dashboard
    public function getRecentPosts($limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT ep.*, e.event_name, u.full_name as posted_by_name 
            FROM event_posts ep
            JOIN events e ON ep.event_id = e.id
            JOIN users u ON ep.posted_by = u.id
            ORDER BY ep.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while($post = $result->fetch_assoc()) {
            $posts[] = $post;
        }
        
        return $posts;
    }
    
    // Get event details with owner info
    public function getEventDetails($eventId) {
        $stmt = $this->conn->prepare("
            SELECT e.*, u.full_name as owner_name, u.avatar as owner_avatar 
            FROM events e 
            JOIN users u ON e.event_owner_id = u.id 
            WHERE e.id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $event = $result->fetch_assoc();
        $event['stats'] = $this->getEventStats($eventId);
        
        return $event;
    }
    
    // Delete post and associated media
    public function deletePost($postId) {
        $this->conn->begin_transaction();
        
        try {
            // Delete likes
            $stmt = $this->conn->prepare("DELETE FROM event_post_likes WHERE post_id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            
            // Delete comments
            $stmt = $this->conn->prepare("DELETE FROM event_post_comments WHERE post_id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            
            // Delete media
            $stmt = $this->conn->prepare("DELETE FROM event_post_media WHERE post_id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            
            // Delete post
            $stmt = $this->conn->prepare("DELETE FROM event_posts WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Post deleted successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update post
    public function updatePost($postId, $title, $content, $postType, $media = []) {
        $this->conn->begin_transaction();
        
        try {
            // Update post
            $stmt = $this->conn->prepare("
                UPDATE event_posts 
                SET title = ?, content = ?, post_type = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $title, $content, $postType, $postId);
            $stmt->execute();
            
            // If new media provided, replace old media
            if (!empty($media)) {
                // Delete old media
                $deleteStmt = $this->conn->prepare("DELETE FROM event_post_media WHERE post_id = ?");
                $deleteStmt->bind_param("i", $postId);
                $deleteStmt->execute();
                
                // Insert new media
                foreach ($media as $index => $mediaItem) {
                    $mediaStmt = $this->conn->prepare("
                        INSERT INTO event_post_media (post_id, media_url, media_type, caption, display_order) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $mediaType = $this->getMediaType($mediaItem['url']);
                    $mediaStmt->bind_param("isssi", $postId, $mediaItem['url'], $mediaType, $mediaItem['caption'], $index);
                    $mediaStmt->execute();
                }
            }
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Post updated successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Search posts
    public function searchPosts($query, $eventId = null, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$query}%";
        
        if ($eventId) {
            $stmt = $this->conn->prepare("
                SELECT ep.*, e.event_name, u.full_name as posted_by_name,
                       (SELECT COUNT(*) FROM event_post_likes WHERE post_id = ep.id) as likes_count,
                       (SELECT COUNT(*) FROM event_post_comments WHERE post_id = ep.id) as comments_count
                FROM event_posts ep
                JOIN events e ON ep.event_id = e.id
                JOIN users u ON ep.posted_by = u.id
                WHERE ep.event_id = ? AND (ep.title LIKE ? OR ep.content LIKE ?)
                ORDER BY ep.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("issii", $eventId, $searchTerm, $searchTerm, $perPage, $offset);
        } else {
            $stmt = $this->conn->prepare("
                SELECT ep.*, e.event_name, u.full_name as posted_by_name,
                       (SELECT COUNT(*) FROM event_post_likes WHERE post_id = ep.id) as likes_count,
                       (SELECT COUNT(*) FROM event_post_comments WHERE post_id = ep.id) as comments_count
                FROM event_posts ep
                JOIN events e ON ep.event_id = e.id
                JOIN users u ON ep.posted_by = u.id
                WHERE ep.title LIKE ? OR ep.content LIKE ?
                ORDER BY ep.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("ssii", $searchTerm, $searchTerm, $perPage, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while($post = $result->fetch_assoc()) {
            $post['media'] = $this->getPostMedia($post['id']);
            $posts[] = $post;
        }
        
        // Get total count
        if ($eventId) {
            $countStmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM event_posts 
                WHERE event_id = ? AND (title LIKE ? OR content LIKE ?)
            ");
            $countStmt->bind_param("iss", $eventId, $searchTerm, $searchTerm);
        } else {
            $countStmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM event_posts 
                WHERE title LIKE ? OR content LIKE ?
            ");
            $countStmt->bind_param("ss", $searchTerm, $searchTerm);
        }
        
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
        
        return [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'query' => $query
        ];
    }
}
?>