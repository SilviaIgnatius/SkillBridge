<?php
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];

    // Include necessary files
    require_once '../classes/DbConnector.php';
    require_once '../classes/Post.php';

    // Get database connection
    $con = \classes\DbConnector::getConnection();

    // Retrieve posts for the logged-in user
    $posts = \classes\Post::retrieveUserPosts($con, $userId);

    // Function to format post date
    function formatPostDate($created_at) {
        $postTime = strtotime($created_at);
        return date('M j, Y \a\t g:i a', $postTime);
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Create Post</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
            <link rel="stylesheet" href="../css/postFeed.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
            <script>
                function toggleLike(postId) {
                    console.log('Toggling like for postId:', postId);

                    fetch('toggle_like.php?postId=' + postId, {
                        method: 'GET'
                    })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Response:', data);

                                if (data.success) {
                                    const likeCounter = document.getElementById('likeCounter_' + postId);
                                    likeCounter.innerHTML = data.likes;
                                } else {
                                    console.error('Failed to toggle like.');
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                            });
                }
            </script>
        </head>
        <body>
            <div class="container create-post">
                <div class="share-wrapper">
                    <div class="share-top">
                        <img src="https://images.unsplash.com/photo-1607346256330-dee7af15f7c5?auto=format&fit=crop&q=80&w=1000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8aW5kaWFuJTIwbWFufGVufDB8fDB8fHww" alt="Profile" class="share-profile-image rounded-circle">
                        <input type="text" placeholder="What is on your mind?" class="share-input form-control border-0 " id="openPopup" data-bs-toggle="modal" data-bs-target="#popupModal"/>
                        <div class="modal" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="create_post.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <img src="https://images.unsplash.com/photo-1607346256330-dee7af15f7c5?auto=format&fit=crop&q=80&w=1000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8aW5kaWFuJTIwbWFufGVufDB8fDB8fHww" alt="Profile" class="popup-profile-image rounded-circle">
                                            <span class="popup-profile-name">
                                                <input type="text" name="username" value="<?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?>" />
                                            </span>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="color: black;">
                                            <textarea name="post" placeholder="What's on your mind?" class="popup-input form-control"></textarea>
                                            <div class="popup-options">
                                                <div class="popup-option" onclick="document.getElementById('videoInput').click();">
                                                    <i class="material-icons popup-icon video-icon">video_camera_front</i>
                                                    <span class="popup-option-text">Video</span>
                                                </div>
                                                <div class="popup-option" onclick="document.getElementById('imageInput').click();">
                                                    <i class="material-icons popup-icon photo-icon">perm_media</i>
                                                    <span class="popup-option-text">Photo</span>
                                                </div>
                                            </div>                               
                                        </div>
                                        <div class="modal-footer">
                                            <input type="file" name="video_path" accept="video/*" id="videoInput" style="display: none">
                                            <input type="file" name="image_path" accept="image/*" id="imageInput" style="display: none">
                                            <input type="submit" name="submit" class="btn btn-primary share-post-button" value="Post">
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="share-hr">
                    <div class="share-bottom">
                        <div class="share-options">
                            <div class="share-option">
                                <i class="material-icons share-icon video-icon">video_camera_front</i>
                                <span class="share-option-text">Video</span>
                            </div>
                            <div class="share-option">
                                <i class="material-icons share-icon photo-icon">perm_media</i>
                                <span class="share-option-text">Photo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <?php
    // Loop through retrieved posts and display them
    foreach ($posts as $post) {
        ?>
                <div class="container post">
                    <div class="postWrapper">
                        <!-- Display user information here -->
                        <div class="postTop">
                            <div class="PostTopLeft">
                                <img src="https://images.unsplash.com/photo-1607346256330-dee7af15f7c5?auto=format&fit=crop&q=80&w=1000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8aW5kaWFuJTIwbWFufGVufDB8fDB8fHww" alt="ProfilePic" class="postProfileImg">
                                <span class="postUsername"><?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?></span>
                                <span class="postDate"><?= formatPostDate($post->created_at); ?></span>
                            </div>
                            <div class="PostTopRight">
                                <button class="btn">
                                    <i class="fas fa-ellipsis-h postVertButton"></i>
                                </button>
                            </div>
                        </div>

                        <div class="postCenter">
                            <p class="postText"><?php echo $post->description; ?></p>
        <?php
        if ($post->image_path) {
            // Display image
            echo '<img src="' . $post->image_path . '" alt="Post Image" class="postImage">';
        } elseif ($post->video_path) {
            // Display video
            echo '<video controls width="100%">
                          <source src="' . $post->video_path . '" type="video/mp4">
                      </video>';
        }
        ?>
                        </div>
                        <hr>
                        <div class="postBottomLeft">
                            <button class="btn btn-danger bottomLeftIcon" onclick="toggleLike(<?php echo $post->post_id; ?>)">
                                <i class="fas fa-heart"></i>
                            </button>
                            <span id="likeCounter_<?php echo $post->post_id; ?>" class="postLikeCounter"><?php echo $post->likes; ?></span>
                        </div>
                    </div>
                </div>
        <?php
    }
    ?>

            <hr class="footerHr">
            <div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            </div>
        </body>
    </html>
    <?php
}
?>