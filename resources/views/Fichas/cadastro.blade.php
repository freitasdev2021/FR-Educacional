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
                <form action="{{route('Fichas/Save')}}" method="POST">
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
                        <!--CADASTRO-->
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Turma</label>
                                    <select class="form-control" name="IDTurma" data-alunos="{{ route('Turmas/AlunosHtml') }}">
                                        <option value="">Selecione</option>
                                        @foreach($Turmas as $t)
                                            <option value="{{$t->id}}" {{isset($Registro) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome}} - {{$t->Serie}}</option>
                                        @endforeach
                                    </select>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Descrição</label>
                                    <input type="text" class="form-control" name="NMConceito" value="{{isset($Registro->NMConceito) ? $Registro->NMConceito : ''}}">
                                </div>
                                <div class="col-sm-6">
                                    <label>Estagio</label>
                                    <select name="Etapa" class="form-control">
                                        <option value="">Selecione</option>
                                        <optgroup label="Bimestre">
                                            <option value="1º BIM" {{ isset($Registro->Etapa) && $Registro->Etapa == "1º BIM" ? 'selected' : '' }}>1º Bimestre</option>
                                            <option value="2º BIM" {{ isset($Registro->Etapa) && $Registro->Etapa == "2º BIM" ? 'selected' : '' }}>2º Bimestre</option>
                                            <option value="3º BIM" {{ isset($Registro->Etapa) && $Registro->Etapa == "3º BIM" ? 'selected' : '' }}>3º Bimestre</option>
                                            <option value="4º BIM" {{ isset($Registro->Etapa) && $Registro->Etapa == "4º BIM" ? 'selected' : '' }}>4º Bimestre</option>
                                        </optgroup>
                                        
                                        <optgroup label="Trimestre">
                                            <option value="1º TRI" {{ isset($Registro->Etapa) && $Registro->Etapa == "1º TRI" ? 'selected' : '' }}>1º Trimestre</option>
                                            <option value="2º TRI" {{ isset($Registro->Etapa) && $Registro->Etapa == "2º TRI" ? 'selected' : '' }}>2º Trimestre</option>
                                            <option value="3º TRI" {{ isset($Registro->Etapa) && $Registro->Etapa == "3º TRI" ? 'selected' : '' }}>3º Trimestre</option>
                                        </optgroup>
                                        
                                        <optgroup label="Semestre">
                                            <option value="1º SEM" {{ isset($Registro->Etapa) && $Registro->Etapa == "1º SEM" ? 'selected' : '' }}>1º Semestre</option>
                                            <option value="2º SEM" {{ isset($Registro->Etapa) && $Registro->Etapa == "2º SEM" ? 'selected' : '' }}>2º Semestre</option>
                                        </optgroup>
                                        
                                        <optgroup label="Periodo">
                                            <option value="1º PER" {{ isset($Registro->Etapa) && $Registro->Etapa == "1º PER" ? 'selected' : '' }}>1º Período</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--ATRIBUIÇÕES DO CARGO-->
                        
                        <div class="col-sm-6" align="center">
                            <label col="col-sm-6">Conceitos</label>
                            <table>
                                <table class="table table-sm tabela">
                                    <thead>
                                      <tr>
                                        <th style="text-align:center;" scope="col">Aluno</th>
                                        <th style="text-align:center;" scope="col">Conceito</th>
                                      </tr>
                                    </thead>
                                    <tbody id="aulaAlunos">
                                        @if(isset($Registro->id))
                                        @foreach($Conceitos as $c)
                                        <tr>
                                            <td><?=$c->Aluno?></td>
                                            <td>
                                                <input type="hidden" value="<?=$c->IDAluno?>" name="Aluno[]">
                                                <input type="text" name="Conceito[]" value="{{$c->Conceito}}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        @if(in_array(Auth::user()->tipo,[4,4.5,6,5,6,5,5.5]))
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        @endif
                        <a class="btn btn-light col-auto" href="{{route('Fichas/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $("select[name=IDTurma]").on("change",function(){
                $.ajax({
                    method : "POST",
                    url : $(this).attr("data-alunos"),
                    data : {
                        IDTurma : $(this).val()
                    },
                    headers : {
                        "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(resp){
                    $("#aulaAlunos").html(resp)
                })
            })
        })
    </script>
</x-educacional-layout>