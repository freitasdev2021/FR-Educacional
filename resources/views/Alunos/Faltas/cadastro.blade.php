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
                <form action="{{route('Alunos/Faltas/Save')}}" method="POST">
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
                            <label>Aluno</label>
                            <select name="IDAluno" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($Alunos as $al)
                                <option value="{{$al->id}}" data-turma="{{$al->IDTurma}}">{{$al->Escola}} - {{$al->Aluno}} - {{$al->Serie}} - {{$al->Turma}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Aula ausente</label>
                            <select name="HashAula" class="form-control" required>
                               
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Jusitficativa</label>
                            <input type="text" class="form-control" name="Justificativa" value="{{isset($Registro) ? $Registro->Justificativa : ''}}" required>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Alunos/Faltas')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $("select[name=IDAluno]").on("change",function(){
            $.ajax({
                method : 'GET',
                url : '/Alunos/Faltas/Faltou/'+$(this).val()+"/"+$("option:selected",this).attr("data-turma")
            }).done(function(response){
                $("select[name=HashAula]").html(response)
            })
        })
    </script>
</x-educacional-layout>