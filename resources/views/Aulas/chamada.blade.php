<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Aulas/Presenca/Todos',$id)}}" class="btn btn-fr">Marcar Todos</a>
                </div>
            </div>
            <hr>
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Aulas/Presenca/list',$id)}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Aluno</th>
                        <th style="text-align:center;" scope="col">Presente</th>
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
        function setPresenca(IDAluno,HashAula,Status,Rota){
            if(Status){
                St = 0
            }else{
                St = 1
            }
            // alert($('meta[name="csrf-token"]').attr('content'))
            // return false
            //
            $.ajax({
                method : "POST",
                url : Rota,
                data : {
                    IDAluno : IDAluno,
                    HashAula : HashAula,
                    Status : St
                },
                headers : {
                    "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(resp){
                console.log(resp)
            })
        }
    </script>
</x-educacional-layout>