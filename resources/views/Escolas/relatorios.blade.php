<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Relatorio</th>
                        <th style="text-align:center;" scope="col">Tipo</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Alunos Usuários Transporte</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','NMTransporte')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Dependências da Escola</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Dependencias Escola')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Lista de Turmas</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Lista de Turmas')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos por Turma</td>
                        <td>Quantitativo</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Alunos por Turma')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos Matrículados</td>
                        <td>Quantitativo</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Alunos Matriculados')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos do Bolsa Família</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','BolsaFamilia')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos de Recuperação Final</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getRecuperacaoFinal')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos de Recuperação Final por Faltas</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getRecuperacaoFinalFaltas')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Quantidade Alunos de Recuperação Final por série</td>
                        <td>Quantitativo</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getQTRecuperacaoFinal')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Livro de Matrícula</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','LivroMatricula')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Boletim Informativo</td>
                        <td>Nominal e Quantitativo</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getBoletimInformativo')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos do Último Censo</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getAlunosCenso')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Mapa de Notas e Faltas</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','mapaNotas')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Médias Mínimas e Necessárias</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','mediasMinimasNecessarias')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos com Foto</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Alunos com Foto')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Alunos Transferidos</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','Transferidos')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Horários</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','getHorarios')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Desempenho Geral</td>
                        <td>Nominal</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/ImprimirDireto','mapaDesempenhoGeral')}}">Imprimir</a></td>
                      </tr>
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>