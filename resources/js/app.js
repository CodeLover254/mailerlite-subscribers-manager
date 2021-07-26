require('./init');
import DataProcessor from "./data-processor";

$(document).ready(()=>{
   $("#subscribersTable").DataTable({
        processing:true,
        ajax:window.location.origin+'/get-subscribers-list',
        columns:[
            {'data':'email'},
            {'data':'name'},
            {
                'data':'fields',
                'render':function(data){
                    return DataProcessor.extractCountryName(data);
                }
            },
            {
                'data':'date_subscribe',
                'render':function(data){
                    return DataProcessor.extractDate(data)
                }
            },
            {
                'data':'date_subscribe',
                'render':function(data){
                    return DataProcessor.extractTime(data)
                }
            },
            {
                'data':'email',
                'render':function(data){

                    return `<a href="/edit-subscriber/${data}" class="btn btn-sm btn-success">
                            <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-email="${data}">
                             <i class="fas fa-trash"></i> Delete
                            </button>
                           `;
                }
            }
        ]
   }) ;
   $("#subscribersTable tbody").on('click','button.delete-btn',async function(){
       const tableRow = $(this).parents('tr');
       const email = $(this).data('email');
       const response = await axios.post('/delete-subscriber',{'email':email});
       if(response.status!==200){
           alert(response.data.error)
       }else{
           const status = response.data.success;
           if(status){
               tableRow.fadeOut('slow');
           }else{
               alert(response.data.message);
           }
       }
   });
});

