namespace app\controller;

use app\models\Ticket;

class TicketController {
    private $ticketModel;

    public function __construct($db) {
        $this->ticketModel = new Ticket($db);
    }

    public function serveAttachment() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            http_response_code(400);
            die("Bad Request: No image ID provided.");
        }

        $ticketId = intval($_GET['id']);
        $attachment = $this->ticketModel->getAttachmentById($ticketId);

        if (!$attachment || empty($attachment['attachment'])) {
            http_response_code(404);
            die("No image found.");
        }

        $attachmentData = base64_decode($attachment['attachment']);
        $attachmentType = $attachment['attachment_type'];

        header("Content-Type: $attachmentType");
        echo $attachmentData;
        exit;
    }
}
