document.addEventListener("DOMContentLoaded", () => {
    window.delete_borrow = async function(id, event) {
        //get item id
        const targetRow = event.target.closest("tr");
        const itemcell = targetRow.querySelector("td[id-item]");
        const itemId = itemcell ? itemcell.getAttribute("id-item") : "Uknown";

        console.log(`itemid: ${itemId}`);

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            background: "#343a40",
            color: "#ffffff",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = `delete.php?id=${id}&item_id=${itemId}`;
            }
        });
    }


});
