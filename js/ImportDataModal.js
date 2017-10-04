var crevento_url;
var modal_contents = {};
var modal_title_selector = '#import_data_modal>.modal-dialog>.modal-content>.modal-header>h4.modal-title';
var modal_body_selector = '#import_data_modal>.modal-dialog>.modal-content>.modal-body';

function setModalContent(id)
{
	if(typeof crevento_url == 'undefined')
	{
		alert('url not set');
		return;
	}
	
	$(modal_title_selector).html('Infos of: ' + id);
	
	if(id in modal_contents)
	{
		$(modal_body_selector).html(modal_contents[id]);
	}
	else
	{
		$(modal_body_selector).html('Lädt Inhalt. Bitte warten Sie einen Moment...');
		jQuery.ajax(
			{
				url: crevento_url + '&evento_id=' + id,
				success: function(loaded_content)
                {
			        $(modal_body_selector).html(loaded_content);
			        modal_contents[id] = loaded_content;
		        }, 
				failure: function()
				{
					$(modal_body_selector).html('Error on internet connection on loading the data');
				}
			}
		);
	}	
}