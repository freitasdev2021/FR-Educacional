<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <form class="col-sm-12 p-2 row" method="GET">
                @if((Auth::user()->tipo == 4))
                <div class="col-auto">
                    <a href="{{route('Alunos/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                @endif
                @if((in_array(Auth::user()->tipo,[2,2.5])))
                <div class="col-auto">
                    <select name="Status" class="form-control" >
                        <option value="">Selecione o Status</option>
                        <option value="0" {{isset($_GET['Status']) && $_GET['Status'] == '0' ? 'selected' : ''}}>Frequente</option>
                        <option value="1" {{isset($_GET['Status']) && $_GET['Status'] == '1' ? 'selected' : ''}}>Evadido</option>
                        <option value="2" {{isset($_GET['Status']) && $_GET['Status'] == '2' ? 'selected' : ''}}>Desistente</option>
                        <option value="3" {{isset($_GET['Status']) && $_GET['Status'] == '3' ? 'selected' : ''}}>Desligado</option>
                        <option value="4" {{isset($_GET['Status']) && $_GET['Status'] == '4' ? 'selected' : ''}}>Egresso</option>
                        <option value="5" {{isset($_GET['Status']) && $_GET['Status'] == '5' ? 'selected' : ''}}>Transferido Para Outra Rede</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="Escola" class="form-control">
                        <option value="">Selecione a Escola</option>
                        @foreach($Escolas as $es)
                        <option value="{{$es['id']}}" {{isset($_GET['Escola']) && $_GET['Escola'] == $es['id'] ? 'selected' : ''}}>{{$es['Nome']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="submit" value="Filtrar" class="form-control bg-fr text-white">
                </div>
                @endif
                @if((Auth::user()->tipo == 4))
                <div class="col-auto">
                    <select name="Status" class="form-control" required>
                        <option value="">Selecione o Status</option>
                        <option value="0" {{isset($_GET['Status']) && $_GET['Status'] == '0' ? 'selected' : ''}}>Frequente</option>
                        <option value="1" {{isset($_GET['Status']) && $_GET['Status'] == '1' ? 'selected' : ''}}>Evadido</option>
                        <option value="2" {{isset($_GET['Status']) && $_GET['Status'] == '2' ? 'selected' : ''}}>Desistente</option>
                        <option value="3" {{isset($_GET['Status']) && $_GET['Status'] == '3' ? 'selected' : ''}}>Desligado</option>
                        <option value="4" {{isset($_GET['Status']) && $_GET['Status'] == '4' ? 'selected' : ''}}>Egresso</option>
                        <option value="5" {{isset($_GET['Status']) && $_GET['Status'] == '5' ? 'selected' : ''}}>Transferido Para Outra Rede</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="submit" value="Filtrar" class="form-control bg-fr text-white">
                </div>
                <div class="col-auto">
                    <label style="visibility: hidden">a</label>
                    <a href="{{route('Alunos/index')}}" class="btn btn-warning text-white">Limpar Filtros</a>
                </div>
                <hr>
                @endif
            </form>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{ route('Alunos/list') . (isset($_GET['Status']) ? '?Status=' . $_GET['Status'] : '') . (isset($_GET['Escola']) ? '&Escola=' . $_GET['Escola'] : '') }}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Turma</th>
                        @if(in_array(Auth::user()->tipo,[2,2.5]))<th style="text-align:center;" scope="col">Escola</th>@endif
                        <th style="text-align:center;" scope="col">Serie</th>
                        <th style="text-align:center;" scope="col">Data de Nascimento</th>
                        <th style="text-align:center;" scope="col">Matrícula</th>
                        <th style="text-align:center;" scope="col">Situação</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        function renovarMatricula(id){
            $("#aluno_"+id).html()
        }
    </script>
</x-educacional-layout>