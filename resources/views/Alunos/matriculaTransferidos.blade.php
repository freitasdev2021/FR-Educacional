<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Transferidos/Matricular')}}" method="POST">
                @csrf
                @if(session('error'))
                <div class="col-sm-12 shadow p-2 bg-danger text-white">
                   <strong>{{session('error')}}</strong>
                </div>
                <br>
                @endif
                <div class="col-sm-12 p-2">
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{!isset($Registro->Foto) ? asset('img/kidAvatar.png') : url("storage/organizacao_".Auth::user()->id_org."alunos/aluno_$Registro->CDPasta/$Registro->Foto")}}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <input type="hidden" value="{{$Registro->IDTransferencia}}" name="IDTransferencia">
                        <input type="hidden" value="{{$Registro->IDAluno}}" name="IDAluno">
                        <div class="col-sm-12">
                            <label>Destino do Aluno</label>
                            <select name="IDTurma" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="0">Reprovar Matrícula</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}">{{$t->Nome." (".$t->Serie.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Feedback</label>
                            <textarea name="Feedback" class="form-control"></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/Transferidos')}}">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $('input[name=CEP]').on("change",function(e){
            if( $(this).val().length == 9){
                var cep = $(this).val();
                var url = "https://viacep.com.br/ws/"+cep+"/json/";
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(dados){
                        $("input[name=UF]").val(dados.uf).change();
                        $("input[name=Cidade]").val(dados.localidade);
                        $("input[name=Bairro]").val(dados.bairro);
                        $("input[name=Rua]").val(dados.logradouro);
                    }
                })
            }            
        })
        //
        $("input[name=CEP]").inputmask('99999-999')
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
        $("input[name=CLResponsavel]").inputmask('(99) 9 9999-9999')
        $("input[name=CPF]").inputmask('999.999.999-99')
        $("input[name=CPFAluno]").inputmask('999.999.999-99')
        $("input[name=CPFResponsavel]").inputmask('999.999.999-99')
        $("input[name=RGResponsavel]").inputmask('99-999-999')
        $("input[name=RGPais]").inputmask('99.999.999')
        $("input[name=RG]").inputmask('99.999.999')
        //
        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        //
        $('.btn-renovar').on("click",function(){
            if(confirm("Deseja Renovar a Matrícula do Aluno?")){
                $("#formRenova").submit()
            }
        })
        //
    </script>
</x-educacional-layout>