$(document).ready(function() {
    // Mostrar/ocultar formulario de respuesta
    $('.responder-btn').click(function() {
        $(this).closest('.comentario').find('.respuesta-form').toggle();
    });
    
    $('.cancelar-respuesta').click(function() {
        $(this).closest('.respuesta-form').hide();
    });
    
    // Cargar respuestas
    $('.ver-respuestas-btn').click(function() {
        const comentarioId = $(this).data('comentario-id');
        const contenedor = $('#respuestas-' + comentarioId);
        
        if (contenedor.is(':visible')) {
            contenedor.hide();
            $(this).html('<i class="fas fa-comments"></i> Ver respuestas');
        } else {
            $.ajax({
                url: 'comentarios/cargar_respuestas.php',
                type: 'GET',
                data: { comentario_padre_id: comentarioId },
                success: function(data) {
                    contenedor.html(data).show();
                    $(this).html('<i class="fas fa-comments"></i> Ocultar respuestas');
                }
            });
        }
    });
    
    // Enviar respuesta
    $('.form-respuesta').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            url: 'comentarios/agregar_comentario.php',
            type: 'POST',
            data: form.serialize(),
            success: function() {
                form.find('textarea').val('');
                form.hide();
                // Recargar respuestas si estaban visibles
                const contenedor = form.closest('.comentario').find('.respuestas-container');
                if (contenedor.is(':visible')) {
                    contenedor.load('comentarios/cargar_respuestas.php', {
                        comentario_padre_id: form.find('[name="comentario_padre_id"]').val()
                    });
                }
            }
        });
    });
    
    // Cargar más comentarios
    $('#cargar-mas-btn').click(function() {
        const btn = $(this);
        const pagina = btn.data('pagina');
        const noticiaId = btn.data('noticia-id');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: 'comentarios/cargar_mas.php',
            type: 'GET',
            data: { 
                noticia_id: noticiaId,
                pagina: pagina
            },
            success: function(data) {
                $('#lista-comentarios').append(data);
                btn.remove();
            }
        });
    });
    
    // Sistema de votos (likes/dislikes)
    $('.like-btn, .dislike-btn').click(function() {
        if (!usuarioLogueado) {
            alert('Debes iniciar sesión para votar');
            return;
        }
        
        const tipo = $(this).hasClass('like-btn') ? 'like' : 'dislike';
        const comentarioId = $(this).data('comentario-id');
        const elemento = $(this);
        
        $.ajax({
            url: 'comentarios/votar_comentario.php',
            type: 'POST',
            data: { 
                comentario_id: comentarioId,
                tipo: tipo
            },
            success: function(data) {
                const resultado = JSON.parse(data);
                if (resultado.success) {
                    $('#like-count-' + comentarioId).text(resultado.likes);
                    $('#dislike-count-' + comentarioId).text(resultado.dislikes);
                    
                    // Resaltar el voto del usuario
                    $('.like-btn, .dislike-btn').removeClass('text-primary text-danger');
                    if (resultado.user_vote === 'like') {
                        $('.like-btn[data-comentario-id="' + comentarioId + '"]').addClass('text-primary');
                    } else if (resultado.user_vote === 'dislike') {
                        $('.dislike-btn[data-comentario-id="' + comentarioId + '"]').addClass('text-danger');
                    }
                }
            }
        });
    });
});