<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class TesteController extends Controller
{
    /**
     * Display a simple test message.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['message' => 'TesteController is working!']);
    }

    /**
     * Display a greeting message.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function greet($name)
    {
        return response()->json(['message' => "Hello, $name! Welcome to TesteController."]);
    }

    /**
     * Handle a POST request and return the received data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handlePost(Request $request)
    {
        $data = $request->all();
        return response()->json(['received_data' => $data]);
    }

}