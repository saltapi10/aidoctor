<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Laravel storage helper
use GuzzleHttp\Client; // Guzzle HTTP client for cURL replacement
use GuzzleHttp\Exception\RequestException; // Guzzle exception handling
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Session;

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

        Session::forget('chatHistory');

        //$prompt = 'Describe the file if it is provided and give medical explanation about it or else answer medical question.Disclaimer it is only for research purposes.';
        $prompt = 'Answer medical questions.Disclaimer it is only for research purposes.';

        session::put('chatHistory', [
            'role' => 'user',
            'parts' => [
                ['text' => $prompt],
            ],
        ]);

        return view('chat');
    }

    public function chatGeminiModule(Request $request){
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

    public function chatAnswer(Request $request)
    {
        $apiKey = config('api_key'); // Replace with your actual API key
        // Validate the file upload
        if (!$request->hasFile('file')) {
            //return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $message = $request->get('message');

        // Validate file upload
        if (!$file) {
            //return response()->json(['error' => 'No file uploaded'], 400);
        }

        if ($file) {
            // Get file information
            $mimeType = $file->getMimeType();
            $numBytes = $file->getSize();
            $displayName = $file->getClientOriginalName(); // Use original filename

            // Upload the file to Laravel storage (optional, adjust path as needed)
            $storedPath = Storage::disk('public')->put('uploads', $file);

            // Prepare content generation request data
            $fileUri = storage_path() . '/app/public/' . $storedPath;

            $fileData = base64_encode(file_get_contents($fileUri));

            //$prompt = 'Describe the file if it is provided and give medical explanation/diagnosis about it or else answer medical question.Disclaimer it is only for research purposes.';

            $content = [
                'contents' => [
                    Session::get('chatHistory') ?? [],
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $message],
                            ['inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $fileData,
                            ]],
                        ],
                    ],
                ],
            ];

            $userHistory = [
                'role' => 'user',
                'parts' => [
                    ['text' => $message],
                    ['inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => $fileData,
                    ]],
                ],
            ];

        }else{
            $content = [
                'contents' => [
                    Session::get('chatHistory') ?? [],
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $message],
                        ],
                    ],
                ],
            ];

            $userHistory = [
                'role' => 'user',
                'parts' => [
                    ['text' => $message],
                ],
            ];
        }

        // Perform content generation request with Guzzle (avoid external cURL calls)
        $client = new Client();
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

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

        $inHistory = [
            $userHistory ?? [],
            [
                'role' => 'model',
                'parts' => [
                    'text' => $generatedText
                ]
            ],
        ];

        Session::put('chatHistory', $inHistory);

        $generatedText = str_replace("*", "<br>", $generatedText);

        if ($file) {
            //remove junk
            unlink(storage_path() . '/app/public/' . $storedPath);
        }

        return $generatedText;

    }

}
