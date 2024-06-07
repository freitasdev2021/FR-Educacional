<div class="modal fade" id="renovaAluno" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Anexar Arquivo</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success renovar">Renovar</button>
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
</div>
<script>
    $(".renovar").on("click",function(){
      $("#formRenova").submit();
    })
</script>
  