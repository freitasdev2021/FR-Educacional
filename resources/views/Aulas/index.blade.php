<x-educacional-layout>
    <style>
        .acordiao{
            width:100%;
        }
    </style>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            @if(in_array(Auth::user()->tipo,[6,4,4.5,5.5,5]))
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Aulas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                <form class="row col-auto" method="GET">
                    <div class="col-auto">
                        <select name="IDTurma" class="form-control">
                            <option value="">Filtre pela Turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->id}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->id ? 'selected' : ''}}>{{$t->Serie}} - {{$t->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="Estagio" class="form-control">
                            <option value="">Etapa</option>
                            <optgroup label="Bimestre">
                                <option value="1º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º BIM" ? 'selected' : '' }}>1º Bimestre</option>
                                <option value="2º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º BIM" ? 'selected' : '' }}>2º Bimestre</option>
                                <option value="3º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "3º BIM" ? 'selected' : '' }}>3º Bimestre</option>
                                <option value="4º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "4º BIM" ? 'selected' : '' }}>4º Bimestre</option>
                            </optgroup>
                            
                            <optgroup label="Trimestre">
                                <option value="1º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º TRI" ? 'selected' : '' }}>1º Trimestre</option>
                                <option value="2º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º TRI" ? 'selected' : '' }}>2º Trimestre</option>
                                <option value="3º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "3º TRI" ? 'selected' : '' }}>3º Trimestre</option>
                            </optgroup>
                            
                            <optgroup label="Semestre">
                                <option value="1º SEM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º SEM" ? 'selected' : '' }}>1º Semestre</option>
                                <option value="2º SEM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º SEM" ? 'selected' : '' }}>2º Semestre</option>
                            </optgroup>
                            
                            <optgroup label="Periodo">
                                <option value="1º PER" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º PER" ? 'selected' : '' }}>1º Período</option>
                            </optgroup>
                        </select>
                    </div>
                    @if(in_array(Auth::user()->tipo,[2,2.5,4,4.5,5,5.5]))
                    <div class="col-auto">
                        <select name="Professor" class="form-control">
                            <option value="">Filtre pelo Professor</option>
                            @foreach($Professores as $p)
                            <option value="{{$p->IDProfessor}}">{{$p->Professor}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-auto">
                        <input type="submit" class="btn btn-light form-control" value="Filtrar">
                    </div>
                </form>
            </div>
            <hr>
            @endif
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <!--AQUI VAI O CONTEUDO-->
                <div class="accordion" id="accordionExample">
                    <!--ITEM-->
                    @foreach($Aulas as $key => $a)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{$key}}">
                            <button 
                                class="accordion-button @if($key != 0) collapsed @endif" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{$key}}" 
                                aria-expanded="{{ $key == 0 ? 'true' : 'false' }}" 
                                aria-controls="collapse{{$key}}">
                                {{ $a->DSConteudo }} - {{date('d/m/Y', strtotime($a->DTAula))}} - {{$a->Estagio}} - {{$a->Professor}}
                            </button>
                        </h2>
                        <div 
                            id="collapse{{$key}}" 
                            class="acordiao accordion-collapse collapse @if($key == 0) show @endif" 
                            aria-labelledby="heading{{$key}}" 
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Vieram a Aula:</strong>
                                        <p>{{ 
                                            isset($a->CTAula) && 
                                            ($decodedCTAula = json_decode($a->CTAula)) && 
                                            isset($decodedCTAula[0]->Frequencia) 
                                                ? $decodedCTAula[0]->Frequencia 
                                                : '' 
                                        }}
                                        </p>
                                        <ul>
                                            @if(isset($a->CTAula) && ($ctaItems = json_decode($a->CTAula)) && is_array($ctaItems))
                                                @foreach($ctaItems as $cta)
                                                    <li>{{ $cta->Aula }} - {{ $cta->Disciplina }}</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <h3>Conteúdo:</h3>
                                        <p>
                                            {{$cta->Conteudo}}
                                        </p>
                                    </div>
                                    <div class="col-sm-6">
                                        <form action="{{route('Aulas/Alterar',$a->Hash)}}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="col-sm-12">
                                                <label>Nome</label>
                                                <input type="text" name="DSAula" value="{{explode(' - ',$a->DSAula)[0]}}" class="form-control">
                                            </div>
                                            <div class="col-sm-12">
                                                <label>Descrição da Aula</label>
                                                <textarea class="form-control" name="DSConteudo">{{$a->DSConteudo}}</textarea>
                                            </div>
                                            <div class="d-flex">
                                                <div class="col-sm-6">
                                                    <label>Turma</label>
                                                    <select class="form-control" name="IDTurma">
                                                        <option value="">Selecione</option>
                                                        @foreach($Turmas as $t)
                                                        <option value="{{$t->id}}" {{$a->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome}} - {{$t->Serie}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Etapa</label>
                                                    <select name="Estagio" class="form-control">
                                                        <optgroup label="Bimestre">
                                                            <option value="1º BIM" {{$a->Estagio == "1º BIM" ? 'selected' : ''}}>1º Bimestre</option>
                                                            <option value="2º BIM" {{$a->Estagio == "2º BIM" ? 'selected' : ''}}>2º Bimestre</option>
                                                            <option value="3º BIM" {{$a->Estagio == "3º BIM" ? 'selected' : ''}}>3º Bimestre</option>
                                                            <option value="4º BIM" {{$a->Estagio == "4º BIM" ? 'selected' : ''}}>4º Bimestre</option>
                                                        </optgroup>
                                                        
                                                        <optgroup label="Trimestre">
                                                            <option value="1º TRI" {{$a->Estagio == "1º TRI" ? 'selected' : ''}}>1º Trimestre</option>
                                                            <option value="2º TRI" {{$a->Estagio == "2º TRI" ? 'selected' : ''}}>2º Trimestre</option>
                                                            <option value="3º TRI" {{$a->Estagio == "3º TRI" ? 'selected' : ''}}>3º Trimestre</option>
                                                        </optgroup>
                                                        
                                                        <optgroup label="Semestre">
                                                            <option value="1º SEM" {{$a->Estagio == "1º SEM" ? 'selected' : ''}}>1º Semestre</option>
                                                            <option value="2º SEM" {{$a->Estagio == "2º SEM" ? 'selected' : ''}}>2º Semestre</option>
                                                        </optgroup>
                                                        
                                                        <optgroup label="Periodo">
                                                            <option value="1º PER" {{$a->Estagio == "1º PER" ? 'selected' : ''}}>1º Período</option>
                                                        </optgroup>
                                                    </select>                        
                                                </div>
                                            </div>
                                            @if(in_array(Auth::user()->tipo,[4,5,4.5,5.5]))
                                            <div class="col-sm-12">
                                                <label>Professor</label>
                                                <select class="form-control" name="IDProfessor">
                                                    <option value="">Selecione</option>
                                                    @foreach($Professores as $p)
                                                    <option value="{{$p->IDProfessor}}" {{$a->IDProfessor == $p->IDProfessor ? 'selected' : ''}}>{{$p->Professor}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            <br>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary">Alterar</button>
                                                &nbsp;
                                                <a href="{{route('Aulas/Presenca',$a->Hash)}}" class="btn btn-warning">Lista de Chamada</a>
                                                &nbsp;
                                                <a href="{{route('Aulas/Delete',$a->Hash)}}" class="btn btn-danger">Excluir</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <!--/ITEM-->
                  </div>
                <!--AQUI TERMINA O CONTEUDO-->
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>