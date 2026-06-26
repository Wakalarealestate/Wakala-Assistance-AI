import './bootstrap';

document.addEventListener("DOMContentLoaded", ()=>{
    const message_container = document.getElementById('messages-container');
    const message_input = document.getElementById("user-input");

    message_input.addEventListener("submit", (e)=>{
        e.preventDefault();

        const form_data = new FormData(message_input);

        const message = form_data.get('message');

        if(!message || message.trim() === ''){
            //prompt user for input other than empty
            console.log("empty message");
            return;
        }

        //toggleTypingPrompt();

        processMessage(message);
        message_input.reset();
    });
    
    function renderUserMessage(message){
        const userMessageDiv = document.createElement("div");
        userMessageDiv.className = "user-message-card";
        userMessageDiv.innerHTML = message;

        message_container.appendChild(userMessageDiv);
        scrollToTop();
    }

    function renderConciergeMessage(message){
        const conciergeMessageDiv = document.createElement("div");
        conciergeMessageDiv.className = "concierge-message-card";
        conciergeMessageDiv.innerHTML = message;

        message_container.appendChild(conciergeMessageDiv);
        scrollToTop();
    }

    function scrollToTop(){
        message_container.scrollTop = message_container.scrollHeight;
    }

    async function processMessage(message){
        try {
            renderUserMessage(message);
            const response = await fetch('/api/webhook/test', {
                method: "POST",
                headers: {
                    "Content-Type":"application/json"
                },
                body:JSON.stringify({
                    "channel": "web",
                    "sender": localStorage.getItem("user_id") || null,
                    "message": message
                })
            });

            if(!response.ok){
                // createErrorPrompt();
            }

            //toggleTypingPrompt();

            const data = await response.json();

            if(data.user_id){
                storeUserConversationID(data.user_id);
            }

            const concierge_response = data.response;

            renderConciergeMessage(concierge_response);
        } catch (error) {

            //toggleTypingPrompt();
            console.log(error);
            //createErrorPrompt();
        }
    }

    function storeUserConversationID(id){
        if(id){
            localStorage.setItem('user_id', id);
        }
    }
});