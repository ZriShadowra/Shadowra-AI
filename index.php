<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpicyChat.AI - AI Character Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0a0a;
            color: #fff;
        }

        .header {
            background: #111;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #222;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
        }

        .logo span {
            color: #8b5cf6;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-premium {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: #fff;
        }

        .btn-login {
            background: #222;
            color: #fff;
        }

        .container {
            display: flex;
            height: calc(100vh - 60px);
        }

        .sidebar {
            width: 250px;
            background: #111;
            border-right: 1px solid #222;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h3 {
            font-size: 14px;
            margin-bottom: 15px;
            color: #999;
        }

        .tag-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 6px;
            cursor: pointer;
            background: #1a1a1a;
            transition: background 0.2s;
        }

        .tag-item:hover {
            background: #222;
        }

        .tag-item input[type="checkbox"] {
            margin-right: 10px;
        }

        .tag-count {
            color: #666;
            font-size: 12px;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
        }

        .banner {
            background: linear-gradient(135deg, #1e3a8a, #7c3aed);
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .banner h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .banner p {
            font-size: 18px;
            opacity: 0.9;
        }

        .search-container {
            padding: 20px 40px;
            background: #0f0f0f;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 12px 20px;
        }

        .search-box input {
            flex: 1;
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            outline: none;
        }

        .filters {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            align-items: center;
        }

        .filter-btn {
            padding: 8px 16px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .filter-btn.active {
            background: #8b5cf6;
            border-color: #8b5cf6;
        }

        .results-info {
            padding: 20px 40px;
            color: #999;
            font-size: 14px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 0 40px 40px 40px;
        }

        .card {
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
        }

        .card-image {
            width: 100%;
            height: 350px;
            object-fit: cover;
            background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
        }

        .card-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(139, 92, 246, 0.9);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .card-content {
            padding: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-creator {
            font-size: 13px;
            color: #8b5cf6;
            margin-bottom: 10px;
        }

        .card-description {
            font-size: 13px;
            color: #999;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }

        .card-tag-small {
            padding: 4px 8px;
            background: #2a2a2a;
            border-radius: 4px;
            font-size: 11px;
            color: #aaa;
        }

        .card-stats {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #666;
        }

        .user-menu {
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🌶️ SPICYCHAT<span>.AI</span></div>
        <div class="header-right">
            <a href="subscribe.php" class="btn btn-premium">👑 Get Premium</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user.php" class="user-menu">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <?php else: ?>
                <a href="login.php" class="btn btn-login">Sign In</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>Narrow by tag</h3>
            <input type="text" placeholder="Search tags" style="width: 100%; padding: 8px; background: #1a1a1a; border: 1px solid #333; border-radius: 6px; color: #fff; margin-bottom: 15px;">
            
            <div class="tag-item">
                <label><input type="checkbox"> Male</label>
                <span class="tag-count">190374</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> FemalePOV</label>
                <span class="tag-count">85568</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Female</label>
                <span class="tag-count">81793</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> MalePOV</label>
                <span class="tag-count">61845</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Dominant</label>
                <span class="tag-count">57384</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Romantic</label>
                <span class="tag-count">56269</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> English</label>
                <span class="tag-count">44810</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Drama</label>
                <span class="tag-count">40077</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Fantasy</label>
                <span class="tag-count">35239</span>
            </div>
            <div class="tag-item">
                <label><input type="checkbox"> Anime</label>
                <span class="tag-count">35168</span>
            </div>
        </div>

        <div class="main-content">
            <div class="banner">
                <h1>Take control of every chat</h1>
                <p>and keep your story flowing with simple /cmd instructions.</p>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <span>🔍</span>
                    <input type="text" placeholder="Dive into endless fantasies - start searching!" id="searchInput">
                </div>
                <div class="filters">
                    <button class="filter-btn">⚙️</button>
                    <button class="filter-btn active">NSFW</button>
                    <button class="filter-btn">Trending ▼</button>
                </div>
            </div>

            <div class="results-info">
                327,369 results found in 145ms
            </div>

            <div class="cards-grid" id="cardsGrid">
                <!-- Cards will be generated here -->
            </div>
        </div>
    </div>

    <script>
        const characters = [
            {
                title: "Bip",
                creator: "@yod112398",
                description: "Idk what animal he is... But who cares, he's freakin adorable!",
                tags: ["Friend", "Wholesome", "Furry", "Male"],
                stats: {messages: "8.8k", likes: "626"},
                image: "lime"
            },
            {
                title: "Hashiras",
                creator: "@ellieeee41",
                description: "A new pick me joined to Demon slayer corps...",
                tags: ["Female", "Drama", "LGBTQ+", "FemalePOV", "Anime"],
                stats: {messages: "118.2k", likes: "1254"},
                image: "orange"
            },
            {
                title: "Aodh Nightshade",
                creator: "@tamirika",
                description: "Ancient Dragon of the Abyss",
                tags: ["Original Character", "Adventure", "Mythological"],
                stats: {messages: "119.5k", likes: "508"},
                image: "red"
            },
            {
                title: "Princess Eleonore",
                creator: "@sprovfx",
                description: "Princess Eleonore",
                tags: [],
                stats: {messages: "866", likes: "153"},
                image: "black"
            },
            {
                title: "Spyro",
                creator: "@mexicangodzilla",
                description: "Spyro the dragon version",
                tags: ["Fictional Media", "Action", "Hero", "Comedy", "Submissive"],
                stats: {messages: "9.2k", likes: "503"},
                image: "purple"
            },
            {
                title: "Mona",
                creator: "@peterpanslabyrinth",
                description: "You found a sleeping Girl in your tent at V.O.A",
                tags: ["Comedy", "Female", "Real", "Gothic"],
                stats: {messages: "169.5k", likes: "522"},
                image: "brown"
            }
        ];

        const colors = {
            lime: '#84cc16',
            orange: '#f97316',
            red: '#dc2626',
            black: '#1a1a1a',
            purple: '#a855f7',
            brown: '#92400e'
        };

        function renderCards() {
            const grid = document.getElementById('cardsGrid');
            grid.innerHTML = characters.map(char => `
                <div class="card" onclick="location.href='chat.php?character=${encodeURIComponent(char.title)}'">
                    <div class="card-image" style="background: ${colors[char.image]}"></div>
                    <span class="card-tag">For You</span>
                    <div class="card-content">
                        <div class="card-title">${char.title}</div>
                        <div class="card-creator">${char.creator}</div>
                        <div class="card-description">${char.description}</div>
                        <div class="card-tags">
                            ${char.tags.map(tag => `<span class="card-tag-small">${tag}</span>`).join('')}
                        </div>
                        <div class="card-stats">
                            <span>💬 ${char.stats.messages}</span>
                            <span>👍 ${char.stats.likes}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        renderCards();
    </script>
</body>
</html>
