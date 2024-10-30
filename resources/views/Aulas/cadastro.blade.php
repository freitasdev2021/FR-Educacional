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
                <form action="{{route('Aulas/Save')}}" method="POST">
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
                    @if(isset($Registro->STAula) && $Registro->STAula == 2)
                    <div class="col-sm-12 shadow p-2 bg-warning">
                        <strong>Aula Encerrada</strong>
                    </div>
                    <br>
                    @endif
                    @if(isset($Registro->IDAula))
                    <input type="hidden" name="id" value="{{$Registro->IDAula}}">
                    @endif
                    <input type="hidden" name="TPAula" value="Normal">
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma" {{(isset($Registro->IDTurma)) ? 'disabled' : 'required'}}>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->IDTurma}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie ." - ".$t->Escola.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Disciplina</label>
                            <select class="form-control" name="IDDisciplina" {{(isset($Registro->IDDisciplina)) ? 'disabled' : 'required'}}>
                                <option value="{{isset($Registro->IDDisciplina) ? $Registro->IDDisciplina : ''}}" {{isset($Registro->NMDisciplina) ? 'selected' : ''}}>{{isset($Registro->NMDisciplina) ? $Registro->NMDisciplina : ''}}</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Conteudo</label>
                            <select class="form-control" name="DSConteudo" {{(isset($Registro->DSConteudo)) ? 'disabled' : 'required'}}>
                                <option value="{{(isset($Registro->DSConteudo)) ? $Registro->DSConteudo : ''}}">{{(isset($Registro->DSConteudo)) ? $Registro->DSConteudo : ''}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 externo">
                            <label>Conteúdo do Planejamento Externo</label>
                            <input type="text" name="DSConteudo" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição da Aula</label>
                            <textarea name="DSAula" class="form-control" {{(isset($Registro->DSAula)) ? 'disabled' : 'required'}}>{{(isset($Registro->DSAula)) ? $Registro->DSAula : ''}}</textarea>
                        </div>
                    </div>
                    <input type="hidden" name="Estagio">
                    <br>
                    <div class="col-sm-12 text-left row">
                        @if(!isset($Registro->STAula) || $Registro->STAula < 2)
                        <button type="submit" class="btn {{(isset($Registro->STAula) && $Registro->STAula == 1) ? 'btn-danger' : 'btn-fr'}} col-auto">{{(isset($Registro->STAula) && $Registro->STAula == 1) ? 'Encerrar' : 'Salvar'}}</button>
                        @endif
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Aulas/index')}}">Voltar</a>
                    </div>
                </form>
                <script>
                    $(".externo").hide()
                    //SELECIONA AS DISCIPLINAS
                    $("select[name=IDTurma]").on("change",function(){
                       $.ajax({
                          method : 'GET',
                          url : "/Professores/DisciplinasProfessor/"+$(this).val()
                       }).done(function(response){
                          $("select[name=IDDisciplina]").html(response)
                       })
                    })
                    //SELECIONA OS CONTEUDOS DO PLANEJAMENTO
                    $("select[name=IDDisciplina]").on("change",function(){
                       $.ajax({
                          method : 'GET',
                          url : "/Planejamentos/getConteudo/"+$(this).val()+"/"+$("select[name=IDTurma]").val()+"/"+$("select[name=TPAula]").val()
                       }).done(function(response){
                          $("select[name=DSConteudo]").html(response)
                          $("input[name=Estagio]").val($("select[name=DSConteudo] option:selected").attr("data-estagio"))
                       })
                    })
                    //SELECIONA OS CONTEUDOS E PEGA O ESTÁGIO
                    $("select[name=DSConteudo]").on("change",function(){
                        $("input[name=Estagio]").val($("option:selected",this).attr("data-estagio"))
                        if($(this).val() == "PDF"){
                            $(".externo").show()
                        }else{
                            $(".externo").hide()
                        }

                        $("input[name=DSConteudo]").val($(this).val())
                        $("input[name=DSConteudo]").attr("value",$(this).val())
                    })
                    //
                </script>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>