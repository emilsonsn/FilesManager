import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {
    const kanbanColumns = document.querySelectorAll('.kanban-c');

    kanbanColumns.forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'ghost',
            draggable: 'a.user-kanban',
            onEnd: function (evt) {
                const cardId = evt.item.getAttribute('data-id');
                const destinationColumnId = evt.to.getAttribute('data-id');
                // Resquisição
                fetch('/demand/product/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product: cardId,
                        status: destinationColumnId
                    })
                })
                .then(response => response)
                .then(data => {
                    console.log('Card updated successfully');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error updating card:', error);
                });
            }
        });
    });
});
