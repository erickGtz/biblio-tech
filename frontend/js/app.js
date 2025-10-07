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

// Espera a que todo el contenido de la página se cargue
document.addEventListener('DOMContentLoaded', () => {

    // 1. Obtenemos los elementos del DOM
    const modal = document.getElementById('document-modal');
    const closeModalBtn = document.getElementById('modal-close-btn');
    // Obtenemos TODOS los botones que pueden abrir el modal
    const openModalBtns = document.querySelectorAll('.open-modal-btn');

    // 2. Función para mostrar el modal
    const openModal = () => {
        modal.classList.add('visible');
    }

    // 3. Función para ocultar el modal
    const closeModal = () => {
        modal.classList.remove('visible');
    }

    // 4. Asignamos los eventos
    // A cada botón de "Ver documento" le decimos que abra el modal al hacer clic
    openModalBtns.forEach(btn => {
        btn.addEventListener('click', openModal);
    });

    // Le decimos al botón de cerrar "X" que cierre el modal
    closeModalBtn.addEventListener('click', closeModal);

    // Opcional: Cierra el modal si el usuario hace clic en el fondo oscuro
    modal.addEventListener('click', (event) => {
        // Solo cierra si el clic fue directamente en el fondo (modal-overlay)
        if (event.target === modal) {
            closeModal();
        }
    });

});
