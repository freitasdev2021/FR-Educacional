<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Secretarias/Save')}}" method="POST">
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
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Organização</label>
                            <input type="text" name="Organizacao" class="form-control @error('Organizacao') is-invalid @enderror" maxlength="100" required value="{{isset($Registro->Organizacao) ? $Registro->Organizacao : ''}}">
                            @error('Organizacao')
                                <div class="text-danger"><strong>{{$message}}</strong></div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>E-mail</label>
                            <input type="email" name="Email" class="form-control @error('Email') is-invalid @enderror" maxlength="50" required value="{{isset($Registro->Email) ? $Registro->Email : ''}}">
                            @error('email')
                            <div class="text-danger"><strong>{{$message}}</strong></div>
                            @enderror
                           
                        </div>
                        <div class="col-sm-3">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" maxlength="9" minlength="9" required value="{{isset($end->CEP) ? $end->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" required value="{{isset($end->Rua) ? $end->Rua : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="60" required value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="60" value="{{isset($end->Bairro) ? $end->Bairro : ''}}" required>
                        </div>
                        <div class="col-sm-2">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($end->UF) ? $end->UF : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-2">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($end->Numero) ? $end->Numero : ''}}" required>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        <button class='btn btn-danger' type="button">Encerrar Contrato</button>
                        <a class="btn btn-light" href="{{route('Secretarias/index')}}">Voltar</a>
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
    </script>
</x-educacional-layout>