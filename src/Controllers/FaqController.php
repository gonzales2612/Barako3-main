<?

namespace App\Controllers;

use App\Models\Faq;
use PDO;

class FaqController extends BaseController
{
    private $faqModel;
    private $db;

    public function __construct($conn = null)
    {
        $this->db = $conn ?: new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for errors
        $this->faqModel = new Faq($this->db);
    }

    // Show the FAQ page
    public function showFaqs()
    {
        // Fetch all FAQs from the model
        $faqs = $this->faqModel->getAllFaqs();

        // Pass data to the view
        $template = 'Faq'; // Name of the view
        $data = ['title' => 'faqTable', 'faqs' => $faqs];

        echo $this->render($template, $data);
    }
}
