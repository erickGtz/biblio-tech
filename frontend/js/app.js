document.addEventListener("DOMContentLoaded", () => {
    const commentList = document.getElementById("comment-list");
    const sendBtn = document.getElementById("send-comment");
    const input = document.getElementById("comment-input");

    sendBtn.addEventListener("click", () => {
        const text = input.value.trim();
        if (text !== "") {
            addComment("Tú", text);
            input.value = "";
        }
    });

    function addComment(author, text) {
        const li = document.createElement("li");
        li.innerHTML = `<strong>${author}:</strong> ${text}`;
        commentList.appendChild(li);
    }
});
