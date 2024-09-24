<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ChatController extends Controller {

    public function chat(Request $request) {
        $request->validate(['message' => 'required|string']);

        // Call the Python script
        $process = new Process(['python3', base_path('/python/chat.py'), $request->input('message')]);
        $process->run();

        // Get the output and return it as a response

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $process->getErrorOutput()], 500);
        }

        $output = $process->getOutput();
        return response()->json(['response' => trim($output)]);
    }

}
