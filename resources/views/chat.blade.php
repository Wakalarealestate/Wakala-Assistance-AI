@extends('layouts.app')
@section('content')
    <div class="min-h-dvh flex flex-col bg-white">
        <!--Navbar section-->
        <div class="h-16 flex items-center justify-between px-4 shrink-0 bg-[#033600]">
            <div class="logo-back-icon min-w-[100px] flex items-center gap-5">
                <img src="{{ asset('img/arrow.png') }}" alt="Nav arrow" class="w-5 h-5 invert">

                <img src="{{ asset('img/logo.png') }}" alt="Wakala Logo" class="w-10 h-10 p-2 rounded-[50%] object-contain bg-white">

                <h2 style="font-size: 1rem; color: #FFF;">Wakala Assistant</h2>
            </div>
            <div class="chat-actions flex flex-row gap-5">
                <img src="{{ asset('img/call.png') }}" alt="call Wakala" class="w-5 h-5 invert">
                <img src="{{ asset('img/dots.png') }}" alt="more actions" class="w-5 h-5 invert">
            </div>
        </div>

        <!--Messages container-->
        <div id="messages-container" class="messages-container flex flex-col flex-1 overflow-y-auto px-4 py-4 pb-28 gap-y-2">

        </div>


        <!--Chatbox section-->
        <div class="message-box fixed bottom-0 left-0 right-0 p-4 z-10 min-h-0">
            <div class="input-group bg-white rounded-full flex items-center flex-1 gap-3 px-4 py-2 border border-[#033600] min-h-0">
                <form id="user-input" class="flex items-center w-full">
                    <input
                        type="text"
                        id="message"
                        name="message"
                        placeholder="Type your message..."
                        class="flex-1 border-0 outline-none bg-transparent min-w-0 text-pretty min-h-0 text-wrap"
                    >

                    <button id="send-btn" type="submit" class="cursor-pointer shrink-0 w-10 h-10 rounded-[50%] bg-[#033600] overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('img/send.png') }}" alt="Send" class="w-5 h-5 object-contain invert">
                    </button>
                </form>
            </div>
        </div>

        <!-- No surplus words or unnecessary actions. - Marcus Aurelius -->
    </div>
@endsection
