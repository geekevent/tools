$('#deleteModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget) // Button that triggered the modal
    let itemName = button.data('name');
    let itemRoute = button.data('route');
    let modal = $(this)

    modal.find('#item-name').text(itemName)
    let items = modal.find('a#link');
    if (items[0] === undefined) {
        return;
    }
    let item = items[0];
    item.href = itemRoute;
})