<?php $pageStyles = '
    .layout { display: grid; grid-template-columns: 200px 1fr; gap: 16px; align-items: start; }
    .card.compact { padding: 20px; }
    .card.compact h2 { margin-bottom: 12px; }
    .tables-list { list-style: none; }
    .tables-list li a { display: block; padding: 6px 8px; border-radius: 6px; font-family: "SF Mono","Fira Code",monospace; font-size: 0.8rem; color: #8b9fc6; text-decoration: none; transition: background 0.15s, color 0.15s; }
    .tables-list li a:hover { background: #1c2333; color: #58a6ff; }
    textarea { width: 100%; min-height: 140px; background: #0f1117; border: 1px solid #2d3548; border-radius: 8px; color: #e1e4e8; font-family: "SF Mono","Fira Code",monospace; font-size: 0.875rem; padding: 12px; outline: none; resize: vertical; line-height: 1.5; transition: border-color 0.2s; }
    textarea:focus { border-color: #58a6ff; }
    .btn-run { margin-top: 12px; padding: 9px 22px; background: #238636; border: 1px solid #2ea043; border-radius: 8px; color: #fff; font-size: 0.875rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; }
    .btn-run:hover { background: #2ea043; }
    .chip.green { border-color: #1b4332; color: #2ea043; }
    .chip.red { border-color: #3d1f28; color: #f48771; }
    .result-wrap { overflow-x: auto; }
    table { font-size: 0.82rem; }
    tbody td { font-family: "SF Mono","Fira Code",monospace; font-size: 0.8rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    td.null-val { color: #4a5568; font-style: italic; }
    .affected-msg { color: #2ea043; font-size: 0.875rem; }
    @media (max-width: 700px) { .layout { grid-template-columns: 1fr; } }
'; ?>
<div class="container-wide">
    <h1>Query Runner</h1>

    <div class="layout">
        <div class="sidebar">
            <div class="card compact">
                <h2>Tables (<?= count($tables) ?>)</h2>
                <?php if (empty($tables)): ?>
                    <p style="color:#6b7b9e; font-size:0.875rem;">No tables.</p>
                <?php else: ?>
                <ul class="tables-list">
                    <?php foreach ($tables as $t): ?>
                        <li>
                            <a href="#" onclick="setQuery('SELECT * FROM `<?= htmlspecialchars($t, ENT_QUOTES) ?>` LIMIT 100'); return false;">
                                <?= htmlspecialchars($t) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="card compact">
                <h2>SQL Query</h2>
                <form method="POST" action="/admin/query">
                    <?= CsrfToken::field() ?>
                    <textarea name="query" id="queryInput" placeholder="SELECT * FROM users LIMIT 10;"><?= htmlspecialchars($query) ?></textarea>
                    <button type="submit" class="btn-run">Run Query</button>
                </form>
            </div>

            <?php if ($error): ?>
            <div class="card compact">
                <h2>Error <?php if ($execTime !== null): ?><span style="font-weight:400;color:#4a5568;">(<?= $execTime ?>ms)</span><?php endif; ?></h2>
                <div class="alert alert-error" style="font-family:monospace; font-size:0.82rem; word-break:break-all;"><?= htmlspecialchars($error) ?></div>
            </div>

            <?php elseif ($isSelect && $result !== null): ?>
            <div class="card compact">
                <h2>Results</h2>
                <div class="meta-row">
                    <span class="chip green"><?= $rowCount ?> row<?= $rowCount !== 1 ? 's' : '' ?></span>
                    <?php if ($execTime !== null): ?><span class="chip"><?= $execTime ?>ms</span><?php endif; ?>
                </div>
                <?php if (empty($result)): ?>
                    <p style="color:#6b7b9e; font-size:0.875rem;">No rows returned.</p>
                <?php else: ?>
                <div class="result-wrap">
                    <table>
                        <thead>
                            <tr><?php foreach (array_keys($result[0]) as $col): ?><th><?= htmlspecialchars($col) ?></th><?php endforeach; ?></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                    <?php if ($val === null): ?>
                                        <td class="null-val">NULL</td>
                                    <?php else: ?>
                                        <td title="<?= htmlspecialchars((string)$val) ?>"><?= htmlspecialchars((string)$val) ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <?php elseif ($affected !== null): ?>
            <div class="card compact">
                <h2>Result <?php if ($execTime !== null): ?><span style="font-weight:400;color:#4a5568;">(<?= $execTime ?>ms)</span><?php endif; ?></h2>
                <p class="affected-msg"><?= $affected ?> row<?= $affected !== 1 ? 's' : '' ?> affected.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function setQuery(sql) {
        document.getElementById('queryInput').value = sql;
        document.getElementById('queryInput').focus();
    }
</script>
