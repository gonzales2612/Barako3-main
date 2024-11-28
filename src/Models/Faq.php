<?
namespace App\Models;

use PDO;

class Faq extends BaseModel
{
    public function getAllFaqs()
    {
        $sql = "SELECT * FROM faqs";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        // Fetch all rows as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
