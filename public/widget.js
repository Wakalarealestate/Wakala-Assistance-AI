(function (){
    const script = document.currentScript;

    const token = 'abcdef';

    const baseUrl = new URL(script.src).origin;

    const iframe = document.createElement("iframe");
    iframe.id = "wakala-widget-chat";

    iframe.src = `${baseUrl}/widget/chat?token=${token}`;

    iframe.style.cssText = `
        width: 100dvw;
        height: 100dvh;
        border: none;
        z-index: 10000;
        background: #FFF;
    `;

    document.body.appendChild(iframe);
})();