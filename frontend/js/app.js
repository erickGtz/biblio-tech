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

document.addEventListener("DOMContentLoaded", () => {

    const commentList = document.getElementById("comment-list");
    const sendBtn = document.getElementById("send-comment");
    const commentInput = document.getElementById("comment-input");

    const idApunte = 1;   // <-- PON AQUÍ EL ID del apunte que se está mostrando
    const idUsuario = 2;  // <-- EL USUARIO LOGGEADO (poner desde sesión)

    // -------------------------------------------------------------------
    // Cargar comentarios existentes
    // -------------------------------------------------------------------
    function cargarComentarios() {
        fetch(`../backend/controllers/comentariosController.php?idApunte=${idApunte}`)
            .then(res => res.json())
            .then(comentarios => {
                commentList.innerHTML = ""; // limpiar lista

                comentarios.forEach(c => {
                    agregarComentarioDOM(
                        c.nombre + " " + c.apellido_paterno + " " + c.apellido_materno,
                        c.contenido,
                        c.fecha_comentario
                    );
                });
            });
    }

    // -------------------------------------------------------------------
    // Enviar comentario
    // -------------------------------------------------------------------
    sendBtn.addEventListener("click", () => {
        const texto = commentInput.value.trim();
        if (texto === "") return;

        const formData = new FormData();
        formData.append("comentario", texto);
        formData.append("idUsuario", idUsuario);
        formData.append("idApunte", idApunte);

        fetch("../backend/controllers/comentariosController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            agregarComentarioDOM(data.nombre, data.contenido, data.fecha);
            commentInput.value = "";
        });
    });

    // -------------------------------------------------------------------
    // Función que agrega comentarios al DOM
    // -------------------------------------------------------------------
    function agregarComentarioDOM(autor, texto, fecha) {

        const fechaFormateada = new Date(fecha).toLocaleString("es-MX", {
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        });

        const li = document.createElement("li");
        li.classList.add("comment-item");
        li.innerHTML = `
            <div class="comment-header">
                <img src="https://i.pravatar.cc/30?u=${autor}" class="comment-avatar">
                <div class="comment-author-info">
                    <span class="comment-author-name">${autor}</span>
                    <span class="comment-timestamp">${fechaFormateada}</span>
                </div>
            </div>
            <p class="comment-body">${texto}</p>
            <div class="comment-actions">
                <button class="action-btn useful"><i class="bi bi-hand-thumbs-up"></i> Útil</button>
                <button class="action-btn not-useful"><i class="bi bi-hand-thumbs-down"></i> No útil</button>
            </div>
        `;

        commentList.prepend(li);
    }

    // Cargar al inicio
    cargarComentarios();

});

