<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDProfessor)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Professores/Apoio/Save')}}" method="POST">
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
                    <input type="hidden" name="IDProfessor" value="{{$IDProfessor}}">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Aluno</label>
                            <select name="IDAluno" class="form-control">
                                <option>Selecione</option>
                                @foreach($Alunos as $a)
                                    <option value="{{$a->id}}" {{(isset($Registro->IDAluno) && $Registro->IDAluno == $a->id) ? 'selected' : ''}}>{{$a->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição do Acompanhamento</label>
                            <textarea name="DSAcompanhamento" class="form-control">{{isset($Registro->DSAcompanhamento) ? $Registro->DSAcompanhamento : ''}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Data de Início</label>
                            <input type="date" name="DTInicio" class="form-control" maxlength="50" required value="{{isset($Registro->DTInicio) ? $Registro->DTTInicio : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Término do Acompanhamento</label>
                            <input type="date" name="DTTermino" class="form-control" value="{{isset($Registro->DTTermino) ? $Registro->DTTermino : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Professores/Apoio',$IDProfessor)}}">Voltar</a>
                    </div>
                    <hr>
                </form>
                <form action="{{route('Professores/Apoio/Save')}}" method="POST">
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
                        <textarea name="Evolucao"></textarea>
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
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
    </script>
</x-educacional-layout>