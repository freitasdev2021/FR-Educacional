<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Biblioteca/Leitores/Save')}}" method="POST">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    @if(!empty($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Nascimento</label>
                            <input type="date" class="form-control" name="Nascimento" value="{{isset($Registro) ? $Registro->Nascimento : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Cargo</label>
                            <input type="text" class="form-control" name="Cargo" value="{{isset($Registro) ? $Registro->Cargo : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro) ? $Endereco->CEP : ''}}">
                        </div>
                        <div class="col-sm-9">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" value="{{isset($Registro) ? $Endereco->Rua : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Endereco->Numero : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Endereco->Bairro : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Endereco->Cidade : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Endereco->Cidade : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Biblioteca/Leitores/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $("input[name=CEP]").inputmask('99999-999')
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
    </script>
</x-educacional-layout>