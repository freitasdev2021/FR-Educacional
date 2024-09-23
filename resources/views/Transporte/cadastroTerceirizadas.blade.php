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
                <form action="{{route('Transporte/Terceirizadas/Save')}}" method="POST">
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
                    @if(isset($Registro->IDTerceirizada))
                    <input type="hidden" name="id" value="{{$Registro->IDTerceirizada}}">
                    @endif
                    <input type="hidden" name="Ramo" value="Transportes">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Nome</label>
                            <input type="text" name="Nome" class="form-control" maxlength="50" required value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Celular</label>
                            <input type="text" name="Telefone" class="form-control" value="{{isset($Registro) ? $Registro->Telefone : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-control" maxlength="50" required value="{{isset($Registro->Email) ? $Registro->Email : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>CNPJ</label>
                            <input type="text" name="CNPJ" class="form-control" maxlength="50" required value="{{isset($Registro->CNPJ) ? $Registro->CNPJ : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro->Cidade) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Rua : ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->UF) ? $Registro->Bairro : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->Numero) ? $Registro->UF : ''}}" required>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" value="{{isset($Registro) ? $Registro->Numero : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-5">
                            <label>Termino Contrato</label>
                            <input type="date" name="TerminoContrato" class="form-control" maxlength="50" value="{{isset($Registro) ? $Registro->TerminoContrato : ''}}" minlength="3" required>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Transporte/Terceirizadas/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
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
        $("input[name=Telefone]").inputmask('(99) 9 9999-9999')
        $("input[name=CNPJ]").inputmask('99.999.999/9999-99')
    </script>
</x-educacional-layout>