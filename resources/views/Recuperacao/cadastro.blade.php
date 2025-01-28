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
                <form action="{{route('Aulas/Recuperacao/Save')}}" method="POST">
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
                    @if(isset($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <input name="PontuacaoPeriodo" type="hidden" value="{{(isset($Registros->PontuacaoPeriodo)) ? $Registros->PontuacaoPeriodo : ''}}">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Disciplina</label>
                            <select name="IDDisciplina" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}" {{(isset($Registros->IDDisciplina) && $Registros->IDDisciplina == $d->IDDisciplina) ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Período</label>
                            <select name="Estagio" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="ANUAL" {{isset($Registro) && $Registro->Estagio == "ANUAL" ? 'selected' : ''}}>Anual</option>
                                
                                <optgroup label="Bimestre">
                                    <option value="1º BIM" {{isset($Registros) && $Registros->Estagio == "1º BIM" ? 'selected' : ''}}>1º Bimestre</option>
                                    <option value="2º BIM" {{isset($Registros) && $Registros->Estagio == "2º BIM" ? 'selected' : ''}}>2º Bimestre</option>
                                    <option value="3º BIM" {{isset($Registros) && $Registros->Estagio == "3º BIM" ? 'selected' : ''}}>3º Bimestre</option>
                                    <option value="4º BIM" {{isset($Registros) && $Registros->Estagio == "4º BIM" ? 'selected' : ''}}>4º Bimestre</option>
                                </optgroup>
                                
                                <optgroup label="Trimestre">
                                    <option value="1º TRI" {{isset($Registros) && $Registros->Estagio == "1º TRI" ? 'selected' : ''}}>1º Trimestre</option>
                                    <option value="2º TRI" {{isset($Registro) && $Registros->Estagio == "2º TRI" ? 'selected' : ''}}>2º Trimestre</option>
                                    <option value="3º TRI" {{isset($Registro) && $Registros->Estagio == "3º TRI" ? 'selected' : ''}}>3º Trimestre</option>
                                </optgroup>
                                
                                <optgroup label="Semestre">
                                    <option value="1º SEM" {{isset($Registros) && $Registros->Estagio == "1º SEM" ? 'selected' : ''}}>1º Semestre</option>
                                    <option value="2º SEM" {{isset($Registros) && $Registros->Estagio == "2º SEM" ? 'selected' : ''}}>2º Semestre</option>
                                </optgroup>
                                
                                <optgroup label="Periodo">
                                    <option value="1º PER" {{isset($Registros) && $Registros->Estagio == "1º PER" ? 'selected' : ''}}>1º Período</option>
                                </optgroup>
                            </select>                            
                        </div>
                        @if(empty($id))
                        <div class="col-sm-3">
                            <label>Aluno</label>
                            <select name="IDAluno" class="form-control" required>
                                
                            </select>
                        </div>
                        @else
                        <div class="col-sm-3">
                            <label>Aluno</label>
                            <input type="text" class="form-control" value="{{$Aluno->Nome}}" disabled>
                            <input type="hidden" name="IDAluno" value="{{$Aluno->IDAluno}}">
                        </div>
                        @endif
                        <div class="col-sm-3">
                            <label>Nota do Aluno</label>
                            <input type="text" name="Nota" class="form-control" value="{{(isset($Registros->Nota)) ? $Registros->Nota : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        @if(Auth::user()->tipo == 6)
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        @endif
                        <a class="btn btn-light col-auto" href="{{route('Aulas/Recuperacao/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <script>
                $("select[name=Estagio]").on("change",function(){
                    //console.log("/Alunos/Recuperacao/Lista/'"+$(this).val()+"'/"+$("select[name=IDDisciplina]").val())
                    $.ajax({
                        method : 'GET',
                        url : "/Alunos/Recuperacao/Lista/"+$(this).val()+"/"+$("select[name=IDDisciplina]").val(),
                    }).done(function(response){
                        // console.log(response)
                        // return false
                        dadosRec = jQuery.parseJSON(response);
                        //ALIMENTAR O ARRAY
                        var select = $("select[name=IDAluno]");
                        
                        select.empty();

                        var option = $("<option>", {
                            value: "",    
                            text: "Selecione"    
                        });
                        select.append(option);
                        $.each(dadosRec, function(index, item) {
                            
                            var option = $("<option>", {
                                value: item.IDAluno,    
                                text: item.Aluno,
                                "data-nota": item.Nota    
                            });

                            
                            select.append(option);
                        });
                        //ALIMENTAÇÃO DOS DEMAIS CAMPOS
                        $("select[name=IDAluno]").on("change",function(){
                            $("input[name=PontuacaoPeriodo]").val($("option:selected",this).attr("data-nota"))
                        })
                        //
                    })
                })
            </script>
            <!---//-->
        </div>
    </div>
</x-educacional-layout>