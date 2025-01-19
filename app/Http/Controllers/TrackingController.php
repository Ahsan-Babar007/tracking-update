<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function getMoreTracks(Request $request)
    {
        // Log the incoming request data
        Log::info('Received request for tracking data', [
            'orderDate' => $request->orderDate,
            'orderTime' => $request->orderTime,
            'deliveryDate' => $request->deliveryDate,
            'recipientZip' => $request->recipientZip,
        ]);
    
        // Validate input
        $request->validate([
            'orderDate' => 'required|date',
            'orderTime' => 'required|date_format:H:i',
            'deliveryDate' => 'required|date',
            'recipientZip' => 'required|string|max:10',
        ]);
    
        $endPoint = env('GET_MORE_TRACKS_ENDPOINT', '');
        $token = env('GET_MORE_TRACKS_TOKEN', '');
    
        // Prepare the API body with the input data
        $body = [
            ['shipped_date', '>=', $request->orderDate . ' ' . $request->orderTime],
            ['shipped_date', '<=', now()->format('Y-m-d 23:59:59')],
            ['label_created_date', '>=', $request->orderDate . ' ' . $request->orderTime],
            ['delivery_date', '>=', $request->deliveryDate . ' 00:00:00'],
            ['delivery_date', '<=', $request->deliveryDate . ' 23:59:59'],
            ['service_id', '=', 1],
            ['recipient_zip', '=', $request->recipientZip],
        ];
    
        // Log the prepared API request data
        Log::info('Prepared API request data', [
            'body' => $body,
        ]);
    
        // Call the API
        $client = new Client();
    
        try {
            // Log that we are sending the request
            Log::info('Sending request to external API', [
                'endpoint' => $endPoint . '/track/list',
                'query_params' => ['api_token' => $token, 'limit' => 100, 'offset' => 0],
            ]);
    
            $response = $client->request('POST', $endPoint . '/track/list', [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'query' => ['api_token' => $token, 'limit' => 100, 'offset' => 0],
                'body' => json_encode($body),
            ]);
    
            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);
    
            Log::info('Received response from external API', [
                'statusCode' => $statusCode,
                'response' => $responseBody,
            ]);
    
            if ($statusCode == 200) {
                // Return the results as a JSON response
                return response()->json([
                    'success' => true,
                    'tracks' => $responseBody['data'] ?? [],
                    'error' => $responseBody['error'] ?? null,
                ]);
            } else {
                Log::error('Failed to fetch tracking information', [
                    'statusCode' => $statusCode,
                    'response' => $responseBody,
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch tracking information.',
                    'response' => $responseBody,
                ], $statusCode);
            }
        } catch (\Exception $ex) {
            // Log the exception if the API request fails
            Log::error('API request failed', [
                'error_message' => $ex->getMessage(),
                'exception' => $ex,
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching tracking information.',
            ], 500);
        }
    }
    
}
