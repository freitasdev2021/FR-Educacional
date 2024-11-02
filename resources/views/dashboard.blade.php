<x-educacional-layout>
   @if(Auth::user()->tipo == 0)
    <div class="shadow p-3 dashboard">
       <div class="col-sm-12 row">
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-buildings' ></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Secretarías Ativas</span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-school'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Escolas </span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bx-child'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Alunos </span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-pencil'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Professores </span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Diretores </span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-user'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Usuários</span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
       </div>
    </div>
    @elseif(in_array(Auth::user()->tipo,[2,4,2.5,4.5]))
    <div class="shadow p-3 dashboard">
      <div class="col-sm-12 row">
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-buildings' ></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Matriculas</span>
                  <span class="info-box-number">
                  {{$Matriculas->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-school'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos Cadastrados</span>
                  <span class="info-box-number">
                  {{$Alunos->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bx-child'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos Desistentes</span>
                  <span class="info-box-number">
                  {{$Desistentes->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-pencil'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos Evadidos</span>
                  <span class="info-box-number">
                  {{$Evadidos->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos Transferidos</span>
                  <span class="info-box-number">
                  {{$Transferidos->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos com Alergia</span>
                  <span class="info-box-number">
                  {{$Alergia->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos que Utilizam Transporte</span>
                  <span class="info-box-number">
                  {{$Transporte->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Alunos com NEE</span>
                  <span class="info-box-number">
                  {{$NEE->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Bolsa Familia</span>
                  <span class="info-box-number">
                  {{$BolsaFamilia->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Acompanhamento Médico</span>
                  <span class="info-box-number">
                  {{$AMedico->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Acompanhamento Psicologico</span>
                  <span class="info-box-number">
                  {{$APsicologico->Quantidade}}
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-user'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Usuários</span>
                  <span class="info-box-number">
                  {{$Usuarios->Usuarios}}
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
    @elseif(Auth::user()->tipo == 6)
    <div class="shadow">
      {{-- <pre>
         {{print_r($ficha)}}
      </pre> --}}
      @foreach($ficha as $f)
      <table class="table">
         <thead class="bg-fr text-white">
            <tr align="center">
               <th colspan="4">{{$f->Serie}} - {{$f->Turma}}</th>
            </tr>
           <tr>
             <th scope="col">Dia</th>
             <th scope="col">Turno</th>
             <th scope="col">Escola</th>
             <th scope="col">Disciplina</th>
           </tr>
         </thead>
         <tbody>
         @foreach(json_decode($f->Horarios) as $h)
           <tr>
             <td class="bg-primary text-white" align="center"><strong>{{$h->Dia}}</strong></td>
             <td>{{$h->Inicio}} - {{$h->Termino}}</td>
             <td>{{$h->Escola}}</td>
             <td>{{$h->Disciplina}}</td>
           </tr>
         @endforeach
         </tbody>
       </table>
       @endforeach
    </div>
    @endif
 </x-educacional-layout>