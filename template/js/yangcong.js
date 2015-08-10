
function yangcong_GetResult(loginhash) {
    ajaxpost('yangcongform_'+loginhash, 'return_yangcong_message'+loginhash);
    
}

function yangcong_SetMMem(id, loginhash) {
    $('mmem').value = id;
    ajaxpost('yangcongform_'+loginhash, 'return_yangcong_message'+loginhash);
    
}