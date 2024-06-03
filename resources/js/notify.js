$(document).ready(function() {
    $('.bell a').click(function(event) {
        event.preventDefault(); // Evita que o link navegue para outra página
        $(this).find('.material-symbols-outlined').toggleClass('selected'); // Alterna a classe 'selected' no ícone do sino
        $('.notifys').toggle(); // Alterna a visibilidade da div com as notificações
    });
});
