<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AskDocController extends Controller
{

    public function askDoc(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Check if file is uploaded
            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $file_tmp = $_FILES['file']['tmp_name'];
                $file_name = $_FILES['file']['name'];
                $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

                // Check file type
                $allowed_types = ['txt', 'pdf', 'docx'];
                if (in_array($file_type, $allowed_types)) {
                    // Extract content from file based on its type
                    if ($file_type == 'txt') {
                        $content = file_get_contents($file_tmp);
                    } elseif ($file_type == 'pdf') {
                        // Use a PDF parser library like TCPDF or PDFParser
                        //$content = extractPdfContent($file_tmp);
                    } elseif ($file_type == 'docx') {
                        // Use PHPWord or another library for DOCX parsing
                        //$content = extractDocxContent($file_tmp);
                    }

                    // Send the content to GPT for analysis
                    $analysis = $this->analyzeWithGPT($content);

                    // Display or return analysis results
                    //echo $analysis;
                    echo "<pre>";
                    var_dump($analysis);
                    echo "</pre>";
                    exit;
                } else {
                    echo "Unsupported file type.";
                }
            } else {
                echo "File upload failed.";
            }
        }

        return view('askDoc');
    }

    // Function to send content to GPT model
    function analyzeWithGPT($text) {
        $hotmail_api_key = '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'; // Replace with your actual API key
        $gmail_user_api_key = 'sk-tALqJYYwnZI5G2n2-4LK-KQGJu6xaNcVk1sC86WOW7T3BlbkFJdeu36blba9XbaG9Y0XRgcgLSQxMY34y972V8wmUFUA'; // Replace with your actual API key
        $gmail_project_api_key = 'sk-svcacct-bVZ2LFPPe7EqLvLJK4bDMO-US7QGoY-PaYN4l5WeEDFEDjjkGA4MKQ1ntLsc9s8rgPGB7kFFDRkBtVlQYKT3BlbkFJPakCGClTVgm2O1LEL2bw6B1T0SOl6BSRQwkw7zvEPqnHNEQb9S3I57glJOHfgWEeNoCCMJI5zpT-uhrskA'; // Replace with your actual API key
        $endpoint = 'https://api.openai.com/v1/chat/completions';  // Make sure this is the correct endpoint

        $data = [
            'model' => 'gpt-4o-mini', // Replace with the correct model, e.g., 'gpt-4'
            'prompt' => $text,
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $gmail_project_api_key
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            // Decode the response and display it
            $result = json_decode($response, true);
            if (isset($result['choices'])) {
                echo "Response: " . $result['choices'][0]['text'];
            } else {
                echo "Error in API response: " . $response;
            }
        }

        // Close cURL session
        curl_close($ch);

        return $result;
    }

//    function getMedicalTermDefinition($term) {
//        $api_key = 'YOUR_MEDICAL_API_KEY'; // Medical API key
//        $url = "https://api.medicalterms.com/v1/terms/$term";
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            "Authorization: Bearer $api_key"
//        ]);
//
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        return json_decode($response, true);
//    }
//
//    function validateMedicalTerms($text) {
//        // Extract words (simple example, refine for better accuracy)
//        $words = explode(' ', $text);
//
//        $corrections = [];
//        foreach ($words as $word) {
//            $definition = $this->getMedicalTermDefinition($word);
//            if (!$definition) {
//                $corrections[] = "$word is not a recognized medical term.";
//            }
//        }
//
//        return $corrections;
//    }

}

