<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDProfessor)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            {{-- <pre>
                {{isset($Registro) ? print_r($Registro) : ''}}
            </pre> --}}
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Professores/Turnos/Save')}}" method="POST">
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
                    @if(isset($Registro->IDTurno))
                    <input type="hidden" name="id" value="{{$Registro->IDTurno}}">
                    @endif
                    <input type="hidden" name="IDProfessor" value="{{$IDProfessor}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Escolas</label>
                            <select class="form-control" name="IDEscola">
                                <option value="">Selecione</option>
                                @foreach($Escolas as $e)
                                <option value="{{$e->IDEscola}}" {{isset($Registro) && $Registro->Escola == $e->Escola ? 'selected' : ''}}>{{$e->Escola}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Disciplina</label>
                            <select class="form-control" name="IDDisciplina">
                                <option value="">Selecione</option>
                                @foreach($Disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}" {{isset($Registro) && $Registro->Disciplina == $d->Disciplina ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Inicio</label>
                            <input type="time" name="INITur" class="form-control" required value="{{isset($Registro->Inicio) ? $Registro->Inicio : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Término</label>
                            <input type="time" name="TERTur" class="form-control" required value="{{isset($Registro->Termino) ? $Registro->Termino : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Dia da Semana</label>
                            <select name="DiaSemana" class="form-control">
                                <option value="Segunda"  {{isset($Registro) && $Registro->DiaSemana == "Segunda" ? 'selected' : ''}}>Segunda</option>
                                <option value="Terça"  {{isset($Registro) && $Registro->DiaSemana == "Terça" ? 'selected' : ''}}>Terça</option>
                                <option value="Quarta"  {{isset($Registro) && $Registro->DiaSemana == "Quarta" ? 'selected' : ''}}>Quarta</option>
                                <option value="Quinta"  {{isset($Registro) && $Registro->DiaSemana == "Quinta" ? 'selected' : ''}}>Quinta</option>
                                <option value="Sexta"  {{isset($Registro) && $Registro->DiaSemana == "Sexta" ? 'selected' : ''}}>Sexta</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Turma</label>
                            <select name="IDTurma" class="form-control">
                                @foreach($Turmas as $t)
                                <option value="{{$t->IDTurma}}" {{isset($Registro) && $t->Turma == $Registro->Turma ? 'selected' : ''}}>{{$t->Turma}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Professores/Turnos',$IDProfessor)}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $("select[name=IDEscola]").on("change",function(){
            $.ajax({
                url : '/Escolas/Disciplinas/Get/'+$(this).val(),
                method : "GET"
            }).done(function(ret){
                Escolas = jQuery.parseJSON(ret)
                $("select[name=IDDisciplina]").html("")
                Escolas.forEach((i)=>{
                    $("select[name=IDDisciplina]").append("<option value="+i.IDDisciplina+">"+i.Disciplina+"</option>")
                })
            })
        })
    </script>
</x-educacional-layout>