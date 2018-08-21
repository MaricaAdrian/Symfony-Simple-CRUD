const articles = document.getElementById('table');


if(table)
{
    table.addEventListener('click', e => {
        if(e.target.className === 'btn btn-dark delete-article')
        {
            e.preventDefault();
            if(confirm("Are you sure you want to delete this article"))
            {
                const id = e.target.getAttribute('data-id');

                fetch(`/admin/article/delete/${id}` , {
                    method: 'DELETE'
                }).then(function(response){

                    response.text().then(function(text) {
                        window.location.href = "/admin/" + text;

                    });
                });

            }
        }

    });
}
