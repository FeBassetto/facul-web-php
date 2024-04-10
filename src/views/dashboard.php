<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php?error=Você precisa estar logado para acessar esta página.');
    exit;
}

require '../config/database.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<style>
    .container {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
</style>

<body>
    <div class="container">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h1 class="mt-4 mb-4">Anotações</h1>

        <div class="accordion" id="accordionNotes">
            <?php foreach ($notes as $index => $note): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        id="heading<?php echo $index; ?>">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                data-target="#collapse<?php echo $index; ?>" aria-expanded="true"
                                aria-controls="collapse<?php echo $index; ?>">
                                <?php echo htmlspecialchars($note['title']); ?>
                            </button>
                        </h2>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editNoteModal<?php echo $index; ?>">
                                Editar
                            </button>
                            <form action="../actions/remove_note_action.php" method="POST">
                                <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                                <button type="submit" class="btn btn-link text-danger"
                                    onclick="return confirm('Tem certeza que deseja excluir esta anotação?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-trash" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 2.5a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 .5.5V4h-13V2.5zm13.546 1a.5.5 0 0 0-.5-.5H2.454a.5.5 0 0 0-.5.5v.5h13v-.5zM4.9 7.9a.5.5 0 0 0 .4.2h5.4a.5.5 0 0 0 .4-.2l.7-.9H4.2l.7.9zm9.5 5a1 1 0 0 1-1 1H3.1a1 1 0 0 1-1-1V5.5a1 1 0 0 1 1-1h9.8a1 1 0 0 1 1 1V13z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div id="collapse<?php echo $index; ?>" class="collapse" aria-labelledby="heading<?php echo $index; ?>"
                        data-parent="#accordionNotes">
                        <div class="card-body">
                            <?php echo htmlspecialchars($note['content']); ?>
                            <hr>
                            <small>Criado em:
                                <?php echo date('d/m/Y H:i', strtotime($note['created_at'])); ?>
                            </small>
                            <br>
                            <small>Atualizado em:
                                <?php echo date('d/m/Y H:i', strtotime($note['updated_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Modal for editing a note -->
                <div class="modal fade" id="editNoteModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="editNoteModalLabel<?php echo $index; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editNoteModalLabel<?php echo $index; ?>">Editar Anotação</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="../actions/edit_note_action.php" method="POST">
                                    <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                                    <div class="form-group">
                                        <label for="edit_title">Título:</label>
                                        <input type="text" class="form-control" id="edit_title" name="edit_title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_content">Conteúdo:</label>
                                        <textarea class="form-control" id="edit_content" name="edit_content" rows="3" required><?php echo htmlspecialchars($note['content']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#addNoteModal">
            Adicionar Anotação
        </button>

        <!-- Modal for adding a new note -->
        <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNoteModalLabel">Nova Anotação</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="../actions/add_note_action.php" method="POST">
                            <div class="form-group">
                                <label for="title">Título:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="content">Conteúdo:</label>
                                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar Anotação</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
