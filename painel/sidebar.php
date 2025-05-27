<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion menus" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa-solid fa-brain"></i>
        </div>
        <div class="sidebar-brand-text mx-3" title="Sistema de Gestão Política">CÉREBRO</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dahboard -->
    <li class="nav-item active">
        <a class="nav-link" href="./">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span></a>
    </li>


    <!-- Divider  -->
    <!-- <hr class="sidebar-divider"> -->

    <!-- Heading -->
    <!-- <div class="sidebar-heading">
        Sistema
    </div> -->

    <!--Nav item - Cadastros-->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#solicitacoes"
           aria-expanded="true" aria-controls="solicitacoes">
            <i class="fa-solid fa-user-pen"></i>
            <span>Solicitações</span>
        </a>
        <div id="solicitacoes" class="collapse" aria-labelledby="headingUtilities"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Serviços</h6>
                <?php
                if(in_array('Certidão de Nascimento - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/cn/index.php?categoria=l">Certidão de Nascimento</a>
                <?php
                }
                if(in_array('Registro Geral - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/rg/index.php?categoria=l">Registro Geral </a>
                <?php
                }
                if(in_array('CRAS - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/cras/index.php?categoria=l">CRAS</a>
                <?php
                }
                if(in_array('CR - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/cr/index.php?categoria=l">CR</a>
                <?php
                }
                if(in_array('Psicologia - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/psicologia/index.php?categoria=l">Psicologia</a>
                <?php
                }
                if(in_array('Odontologia - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/odontologia/index.php?categoria=l">Odontologia</a>
                <?php
                }
                if(in_array('Jurídico - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/juridico/index.php?categoria=l">Jurídico</a>
                <?php
                }
                if(in_array('Educação - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/educacao/index.php?categoria=l">Educação</a>
                <?php
                }
                ?>
                <h6 class="collapse-header">Saúde</h6>
                <?php
                if(in_array('Saúde - Visualizar', $ConfPermissoes)){
                $q = "select * from categorias where deletado = '0' order by descricao";
                $r = mysqli_query($con, $q);
                while($c = mysqli_fetch_object($r)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/servicos/saude/index.php?categoria=<?=$c->codigo?>"><?=$c->descricao?></a>
                <?php
                }
                }
                ?>
            </div>
        </div>
    </li>


    <?php
    if(in_array('Ação Social - Visualizar', $ConfPermissoes)){
    ?>
    <li class="nav-item">
        <a remove class="nav-link" href="#" acao="limpar_busca" url="paginas/cadastros/acao_social/index.php">
            <i class="fa-solid fa-users"></i>
            <span>Ação Social</span></a>
    </li>
    <?php
    }
    if(in_array('Ofícios - Visualizar', $ConfPermissoes)){
    ?>
    <li class="nav-item">
        <a remove class="nav-link"  href="#" acao="limpar_busca" url="paginas/cadastros/oficios/index.php">
            <i class="fa-solid fa-file-text"></i>
            <span>Ofícios</span></a>
    </li>
    <?php
    }
    ?>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#cadastros"
           aria-expanded="true" aria-controls="cadastros">
            <i class="fa-solid fa-users-gear"></i>
            <span>Cadastros</span>
        </a>
        <div id="cadastros" class="collapse" aria-labelledby="headingUtilities"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Tipos</h6>
                <?php
                if(in_array('Assessores - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/cadastros/assessores/index.php">Assessores</a>
                <?php
                }
                if(in_array('Beneficiados - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" acao="limpar_busca" url="paginas/cadastros/beneficiados/index.php">Beneficiados</a>
                <?php
                }
                ?>
            </div>
        </div>
    </li>
    <?php
    // if(in_array('Relatórios', $ConfPermissoes)){
    ?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#relatorios"
           aria-expanded="true" aria-controls="relatorios">
           <i class="fas fa-chart-pie"></i>
            <span>Relatórios</span>
        </a>
        <div id="relatorios" class="collapse" aria-labelledby="headingUtilities"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <!--<a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=geral">Geral</a>-->
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=bairros">Bairros</a>
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=servicos">Serviços</a>
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=idade">Idade</a>
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=sexo">Sexo</a>
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=municipios">Municípios</a>
                <a class="collapse-item" href="#" url="paginas/relatorios/index.php?tipo=assessores">Assessores</a>
            </div>
        </div>
    </li>
    <?php
    // }
    ?>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configuracoes"
           aria-expanded="true" aria-controls="configuracoes">
            <i class="fas fa-fw fa-cog"></i>
            <span>Configurações</span>
        </a>
        <div id="configuracoes" class="collapse" aria-labelledby="headingUtilities"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Tabelas</h6>
                <?php
                if(in_array('Fontes Locais - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/fontes_locais/index.php">Fontes Locais</a>
                <?php
                }
                if(in_array('Municípios - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/municipios/index.php">Municípios</a>
                <?php
                }
                if(in_array('Bairros - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/bairros/index.php">Bairros</a>
                <?php
                }

                if(in_array('Secretarias - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/secretarias/index.php">Secretarias</a>
                <?php
                }
                if(in_array('Tipo de Serviço - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/tipo_servico/index.php">Tipo de Serviço</a>
                <?php
                }

                if(in_array('Tipo Ação Social - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/acao_social_tipo/index.php">Tipo Ação Social</a>
                <?php
                }

                if(in_array('Categorias de Serviço - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/categorias/index.php">Categorias de Serviço</a>
                <?php
                }
                if(in_array('Especialidades - Visualizar', $ConfPermissoes)){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/especialidades/index.php">Especialidades</a>
                <?php
                }
                if(in_array('Usuários - Visualizar', $ConfPermissoes) or $_SESSION['usuario']['codigo'] == 1){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/usuarios/index.php">Usuários</a>
                <?php
                }
                if(in_array('Permissoes - Visualizar', $ConfPermissoes) or $_SESSION['usuario']['codigo'] == 1){
                ?>
                <a class="collapse-item" href="#" url="paginas/cadastros/permissoes/index.php">Permissoes</a>
                <?php
                }
                ?>
            </div>
        </div>
    </li>




    <!-- Nav Item - Configuração -->
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
           aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Configurações</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Configurações:</h6>
                <a class="collapse-item" href="buttons.html">Buttons</a>
                <a class="collapse-item" href="cards.html">Cards</a>
            </div>
        </div>
    </li> -->


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>