<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage; // Laravel storage helper
use GuzzleHttp\Client; // Guzzle HTTP client for cURL replacement
use GuzzleHttp\Exception\RequestException; // Guzzle exception handling
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\File;

class ChatController extends Controller {

    public $chat;

    public function test() {
//        print Gemini::generateTextUsingImageFile(
//            'image/jpeg',
//            'elephpant.jpg',
//            'Explain what is in the image',
//        );

        //$thisFile = Storage::disk('local')->get('/uploads/ZuDh16H2yILVkOnO2A7epnpyDaC0wsoxhLUPyMxx.jpg');

        //echo storage_path();exit;

        //$path = storage_path() . "/app/uploads/ZuDh16H2yILVkOnO2A7epnpyDaC0wsoxhLUPyMxx.jpg";

        //$path = storage_path() . "/app/uploads/ZuDh16H2yILVkOnO2A7epnpyDaC0wsoxhLUPyMxx.jpg";
        //echo  File::get($path);
        //exit;

        //$thisFile = Storage::disk('local')->get($path);

        //$fileUri = Storage::disk('local')->url($thisFile);

        //$filePath = storage_path() . '/app/ZuDh16H2yILVkOnO2A7epnpyDaC0wsoxhLUPyMxx.jpg';
        //$thisFile = Storage::get($filePath);

//        $files = Storage::disk('local')->files('public');
//        echo "<pre>";
//        var_dump($files);
//        echo "</pre>";
//        exit;

        //$fileContents = Storage::get('uploads/AhjK15rhUDNNyx2WRnNJtMVQXzuLnF39waXsGS9a.txt');

        //$file = Storage::disk('public')->get('uploads/3vvdTGz3PPmwdpf14b40hZ8f1Yw2IyB842CNW0B8.jpg');

        //$mimeType = Storage::mimeType('uploads/AhjK15rhUDNNyx2WRnNJtMVQXzuLnF39waXsGS9a.txt');
        //echo $mimeType;exit;

//        echo "<pre>";
//        var_dump($file);
//        echo "</pre>";
//        exit;

//        print Gemini::generateTextUsingImageFile(
//            'image/png',
//            storage_path() . '/app/public/uploads/2eIzzznxaIWeQx8ibxsb2HCr9ufZ2DyWgZKQdZht.jpg',
//            'Explain what is in the image',
//        );

        print Gemini::generateTextUsingImageFile(
            'application/pdf',
            storage_path() . '/app/public/uploads/1HpJg063F9CkkFlFjmsdLflDEo9shloDarSntK2F.pdf',
            'Explain what is in the image',
        );

        //$chat = Gemini::startChat();

        //print $chat->sendMessage('Hello World in PHP');

        //echo "<br>";
// echo "Hello World!";
// This code will print "Hello World!" to the standard output.

        //print $chat->sendMessage('in Go');
    }

    public function chat() {

        $this->chat = Gemini::startChat();

        return view('chat');
    }

    public function chatGemini(Request $request){
        // Validate the file upload
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $message = $request->get('message');

        // Validate file upload
        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        // Get file information
        $mimeType = $file->getMimeType();

        // Upload the file to Laravel storage (optional, adjust path as needed)
        $storedPath = Storage::disk('public')->put('uploads', $file);

        $prompt = '"""
    You are a domain expert in medical image analysis. You are tasked with
    examining medical images for a renowned hospital.
    Your expertise will help in identifying or
    discovering any anomalies, diseases, conditions or
    any health issues that might be present in the image.

    Your key responsibilities:
    1. Detailed Analysis : Scrutinize and thoroughly examine each image,
    focusing on finding any abnormalities.
    2. Analysis Report : Document all the findings and
    clearly articulate them in a structured format.
    3. Recommendations : Basis the analysis, suggest remedies,
    tests or treatments as applicable.
    4. Treatments : If applicable, lay out detailed treatments
    which can help in faster recovery.

    Important Notes to remember:
    1. Scope of response : Only respond if the image pertains to
    human health issues.
    2. Clarity of image : In case the image is unclear,
    note that certain aspects are
    "Unable to be correctly determined based on the uploaded image"
    3. Disclaimer : Accompany your analysis with the disclaimer:
    "Consult with a Doctor before making any decisions."
    4. Your insights are invaluable in guiding clinical decisions.
    Please proceed with the analysis, adhering to the
    structured approach outlined above.

    Please provide the final response with these 4 headings :
    Detailed Analysis, Analysis Report, Recommendations and Treatments

"""';

        $prompt = 'You help a doctor with his patients. Provide explanation about this file';

        $prompt = 'Describe this file and provide medical explanation/diagnosis about it.Disclaimer it is only for research purposes.';

        $result = Gemini::generateTextUsingImageFile(
            $mimeType,
            storage_path() . '/app/public/' . $storedPath,
            $prompt.$message,
        );

        $result = str_replace("*", "<br>", $result);

        //remove junk
        unlink(storage_path() . '/app/public/' . $storedPath);

        return $result;
    }

    public function chatAnswerText(Request $request)
    {

        $apiKey = 'AIzaSyA-kUc9rDblZ_IH9lhmHa2vbRlyHvHRm0c'; // Replace with your actual API key
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $request->get('content'),
                        ],
                    ],
                ],
            ],
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            $return =  "Error: " . $curlError;
        } else {
            $responseData = json_decode($response, true);
            // Process the response data here
            //print_r($responseData);

            $return = $responseData['candidates'][0]['content']['parts'][0]['text'];
        }

        return $return;
    }

    public function chatAnswer(Request $request)
    {
        $apiKey = 'AIzaSyA-kUc9rDblZ_IH9lhmHa2vbRlyHvHRm0c'; // Replace with your actual API key
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

        $file = $request->file('file');
        $message = $request->file('message');

        //File Name
        //echo $file->getClientOriginalName();

        //Display File Extension
        //echo $file->getClientOriginalExtension();

        //Display File Real Path
        //echo $file->getRealPath();

        //Display File Size
        //echo $file->getSize();

        //Display File Mime Type
        //echo $file->getMimeType();

        //$handle = fopen($file["tmp_name"], 'r');

        $file_tmp = $file->getPathName();
        $file_name = $file->getClientOriginalName();
        $file_type = $file->getMimeType();

        // Calculate file size
        $numBytes = $file->getSize();

        // Initial upload request
        $ch = curl_init('http:localhost:8005' . '/upload/v1beta/files?key=' . $apiKey);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Goog-Upload-Protocol: resumable',
            'X-Goog-Upload-Command: start',
            'X-Goog-Upload-Header-Content-Length: ' . $numBytes,
            'X-Goog-Upload-Header-Content-Type: '.$file_type,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['file' => ['display_name' => $file_name]]));
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo "Error during upload initiation: " . $curlError;
            exit;
        }

        // Extract upload URL from response headers
        $tmpHeaderFile = 'upload-header.tmp';
        file_put_contents($tmpHeaderFile, $response);
        $uploadUrl = preg_match('/x-goog-upload-url: (.*)/i', file_get_contents($tmpHeaderFile), $matches) ? $matches[1] : null;
        unlink($tmpHeaderFile);

        if (!$uploadUrl) {
            echo "Failed to extract upload URL from response.";
            exit;
        }

        // Upload the file
        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Length:
        ' . $numBytes,
            'X-Goog-Upload-Offset: 0',
            'X-Goog-Upload-Command: upload, finalize',
        ]);
        curl_setopt($ch, CURLOPT_INFILE, fopen($file, 'rb'));
        curl_setopt($ch, CURLOPT_INFILESIZE, $numBytes);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo "Error during file upload: " . $curlError;
            exit;
        }

        // Process upload response
        $fileInfo = json_decode($response, true);
        if (!$fileInfo) {
            echo "Failed to decode upload response.";
            exit;
        }

        $fileUri = $fileInfo['file']['uri'];
        echo "File URI: " . $fileUri . PHP_EOL;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $message],
                        ['file_data' => ['mime_type' => $file->getMimeType(), 'file_uri' => $fileUri]],
                    ],
                ],
            ],
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            $return =  "Error: " . $curlError;
        } else {
            $responseData = json_decode($response, true);
            // Process the response data here
            //print_r($responseData);

            $return = $responseData['candidates'][0]['content']['parts'][0]['text'];
        }

        return $return;
    }

    public function uploadFile(Request $request)
    {
        $apiKey = 'AIzaSyA-kUc9rDblZ_IH9lhmHa2vbRlyHvHRm0c'; // Replace with your actual API key
        // Validate the file upload
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $message = $request->get('message');



        // Validate file upload
        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        // Get file information
        $mimeType = $file->getMimeType();
        $numBytes = $file->getSize();
        $displayName = $file->getClientOriginalName(); // Use original filename

        // Upload the file to Laravel storage (optional, adjust path as needed)
        $storedPath = Storage::disk('public')->put('uploads', $file);

        // Prepare content generation request data
        //$fileUri = Storage::disk('public')->url($storedPath); // Get URL from storage

        //$fileUri = storage_path() . '/app/public/uploads/3vvdTGz3PPmwdpf14b40hZ8f1Yw2IyB842CNW0B8.jpg';

        //echo storage_path() . '/app/public/' . $storedPath;exit;

        $fileUri = storage_path() . '/app/public/' . $storedPath;

        $content = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $message],
                        ['file_data' => ['mime_type' => $mimeType, 'file_uri' => $fileUri]],
                    ],
                ],
            ],
        ];

        // Perform content generation request with Guzzle (avoid external cURL calls)
        $client = new Client();
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        try {
            $response = $client->post($url, [
                'json' => $content,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (RequestException $e) {
            return response()->json(['error' => 'Error during content generation request: ' . $e->getMessage()], 500);
        }

        $responseData = json_decode($response->getBody(), true);

        if (!$responseData) {
            return response()->json(['error' => 'Failed to decode generation response.'], 500);
        }

        // Process and return generated text
        $generatedText = '';
        foreach ($responseData['candidates'] as $candidate) {
            foreach ($candidate['content']['parts'] as $part) {
                $generatedText .= $part['text'];
            }
        }

        return response()->json(['success' => true, 'generated_text' => $generatedText]);

        //storage_path() . '/app/public/uploads/3vvdTGz3PPmwdpf14b40hZ8f1Yw2IyB842CNW0B8.jpg',


//        print Gemini::generateTextUsingImageFile(
//            $file->getMimeType(),
//            $fileUri,
//            'Explain what is in the image',
//        );

    }

}
