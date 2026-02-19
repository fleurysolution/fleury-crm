<?php
//load chat ui if chat module is enabled
echo '<style>
         .init-chat-icon {
          width: 25px;
          height: 25px;
          background-color: #007bff;
          color: white;
          border-radius: 50%;
          position: fixed;
          bottom: 10px;
          right: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          z-index: 1001;
        }
         .chat-min-icon {
          width: 20px; /* Slightly smaller */
          height: 20px; /* Slightly smaller */
          background-color: #007bff; /* Darker shade for differentiation */
          color: white;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          position: fixed;
          z-index: 1001;
        }
        .rise-chat-wrapper {
          width: 300px;
          height: 400px;
          background-color: #f1f1f1;
          border: 1px solid #ccc;
          position: fixed;
          z-index: 1000;
          cursor: grab;
          /* Centering the div */
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }
        .rise-chat-wrapper.hide {
          display: none;
        }
      </style>';
      
$can_chat = can_access_messages_module();

if (get_setting("module_chat") && $can_chat) {
?>


    <div id="js-rise-chat-wrapper" class="rise-chat-wrapper hide"></div>

   
    
    <script type="text/javascript">
        $(document).ready(function() {


            var $chatIconWrapper = $('<div id="js-init-chat-icon" class="init-chat-icon"></div>');
            //allowed data-type= open/close/unread
            $chatIconWrapper.append(' <span id="js-chat-min-icon" data-type="open" class="chat-min-icon"><i data-feather="message-circle" class="icon-18"></i></span>');

            var $chatBoxWrapper = '<div id="js-rise-chat-wrapper" class="rise-chat-wrapper hide"></div>';
            if (isMobile()) {
                $('#mobile-chat-menu-button').append($chatIconWrapper).find(".init-chat-icon").removeClass("init-chat-icon");
                $('#mobile-chat-menu-button').append($chatBoxWrapper);
            } else {
                $('body').append($chatIconWrapper);
                $('body').append($chatBoxWrapper);
            }



            chatIconContent = {
                "open": "<i data-feather='message-circle' class='icon-18'></i>",
                "close": "<i data-feather='x' class='icon-18'></i>",
                "unread": ""
            };

            //we'll wait for 15 sec after clicking on the unread icon to see more notifications again.

            setChatIcon = function(type, count) {

                //don't show count if the data-prevent-notification-count is 1
                if ($("#js-chat-min-icon").attr("data-prevent-notification-count") === "1" && type === "unread") {
                    return false;
                }


                $("#js-chat-min-icon").attr("data-type", type).html(count ? count : chatIconContent[type]);

                if (type === "open") {
                    $("#js-rise-chat-wrapper").addClass("hide"); //hide chat box
                    $("#js-init-chat-icon").removeClass("has-message");
                } else if (type === "close") {
                    $("#js-rise-chat-wrapper").removeClass("hide"); //show chat box
                    $("#js-init-chat-icon").removeClass("has-message");
                } else if (type === "unread") {
                    $("#js-init-chat-icon").addClass("has-message");
                }

            };

            changeChatIconPosition = function(type) {
                if (type === "close") {
                    $("#js-init-chat-icon").addClass("move-chat-icon");
                } else if (type === "open") {
                    $("#js-init-chat-icon").removeClass("move-chat-icon");
                }
            };

            //is there any active chat? open the popup
            //otherwise show the chat icon only
            var activeChatId = getCookie("active_chat_id"),
                isChatBoxOpen = getCookie("chatbox_open"),
                $chatIcon = $("#js-init-chat-icon");


            $chatIcon.click(function() {
                $("#js-rise-chat-wrapper").html("");

                window.updateLastMessageCheckingStatus();

                var $chatIcon = $("#js-chat-min-icon");

                if ($chatIcon.attr("data-type") === "unread") {
                    $chatIcon.attr("data-prevent-notification-count", "1");

                    //after clicking on the unread icon, we'll wait 11 sec to show more notifications again.
                    setTimeout(function() {
                        $chatIcon.attr("data-prevent-notification-count", "0");
                    }, 11000);
                }

                var windowSize = window.matchMedia("(max-width: 767px)");

                if ($chatIcon.attr("data-type") !== "close") {
                    //have to reload
                    setTimeout(function() {
                        loadChatTabs();
                    }, 200);
                    setChatIcon("close"); //show close icon
                    setCookie("chatbox_open", "1");
                    if (windowSize.matches) {
                        changeChatIconPosition("close");
                    }
                } else {
                    //have to close the chat box
                    setChatIcon("open"); //show open icon
                    setCookie("chatbox_open", "");
                    setCookie("active_chat_id", "");
                    if (windowSize.matches) {
                        changeChatIconPosition("open");
                    }
                }

                if (window.activeChatChecker) {
                    window.clearInterval(window.activeChatChecker);
                }

                if (typeof window.placeCartBox === "function") {
                    window.placeCartBox();
                }

                feather.replace();

            });

            //open chat box
            if (isChatBoxOpen) {

                if (activeChatId) {
                    getActiveChat(activeChatId);
                } else {
                    loadChatTabs();
                }
            }

            var windowSize = window.matchMedia("(max-width: 767px)");
            if (windowSize.matches) {
                if (isChatBoxOpen) {
                    $("#js-init-chat-icon").addClass("move-chat-icon");
                }
            }




            $('body #js-rise-chat-wrapper').on('click', '.js-message-row', function() {
                getActiveChat($(this).attr("data-id"));
            });

            $('body #js-rise-chat-wrapper').on('click', '.js-message-row-of-team-members-tab', function() {
                getChatlistOfUser($(this).attr("data-id"), "team_members");
            });

            $('body #js-rise-chat-wrapper').on('click', '.js-message-row-of-clients-tab', function() {
                getChatlistOfUser($(this).attr("data-id"), "clients");
            });


        });

        function getChatlistOfUser(user_id, tab_type) {

            setChatIcon("close"); //show close icon

            appLoader.show({
                container: "#js-rise-chat-wrapper",
                css: "bottom: 40%; right: 35%;"
            });
            $.ajax({
                url: "<?php echo get_uri("messages/get_chatlist_of_user"); ?>",
                type: "POST",
                data: {
                    user_id: user_id,
                    tab_type: tab_type
                },
                success: function(response) {
                    $("#js-rise-chat-wrapper").html(response);
                    appLoader.hide();
                }
            });
        }

        function loadChatTabs(trigger_from_user_chat) {

            setChatIcon("close"); //show close icon

            setCookie("active_chat_id", "");
            appLoader.show({
                container: "#js-rise-chat-wrapper",
                css: "bottom: 40%; right: 35%;"
            });
            $.ajax({
                url: "<?php echo get_uri("messages/chat_list"); ?>",
                data: {
                    type: "inbox"
                },
                success: function(response) {
                    $("#js-rise-chat-wrapper").html(response);

                    if (!trigger_from_user_chat) {
                        $("#chat-inbox-tab-button a").trigger("click");
                    } else if (trigger_from_user_chat === "team_members") {
                        $("#chat-users-tab-button").find("a").trigger("click");
                    } else if (trigger_from_user_chat === "clients") {
                        $("#chat-clients-tab-button").find("a").trigger("click");
                    }
                    appLoader.hide();
                }
            });

        }


        function getActiveChat(message_id) {
            setChatIcon("close"); //show close icon

            appLoader.show({
                container: "#js-rise-chat-wrapper",
                css: "bottom: 40%; right: 35%;"
            });
            $.ajax({
                url: "<?php echo get_uri('messages/get_active_chat'); ?>",
                type: "POST",
                data: {
                    message_id: message_id
                },
                success: function(response) {
                    $("#js-rise-chat-wrapper").html(response);
                    appLoader.hide();
                    setCookie("active_chat_id", message_id);
                    $("#js-chat-message-textarea").focus();
                }
            });
        }

        window.prepareUnreadMessageChatBox = function(totalMessages) {
            setChatIcon("unread", totalMessages); //show close icon
        };


        window.triggerActiveChat = function(message_id) {
            getActiveChat(message_id);
        }
    </script>
    
     <script type="text/javascript">
        
        const chatWrapper = document.getElementById("js-rise-chat-wrapper");
        const chatIcon = document.getElementById("js-init-chat-icon");
        
        let isDragging = false;
        let offsetX, offsetY;
        
        chatWrapper.addEventListener("mousedown", (e) => {
          isDragging = true;
          offsetX = e.clientX - chatWrapper.offsetLeft;
          offsetY = e.clientY - chatWrapper.offsetTop;
          chatWrapper.style.cursor = "grabbing";
        });
        
        document.addEventListener("mousemove", (e) => {
          if (isDragging) {
            const newLeft = e.clientX - offsetX;
            const newTop = e.clientY - offsetY;
        
            // Move the chat window
            chatWrapper.style.left = `${newLeft}px`;
            chatWrapper.style.top = `${newTop}px`;
        
            // Move the chat icon relative to the chat window
            chatIcon.style.left = `${newLeft - 60}px`; // Adjust as needed for positioning
            chatIcon.style.top = `${newTop}px`;
          }
        });
        
        document.addEventListener("mouseup", () => {
          isDragging = false;
          chatWrapper.style.cursor = "grab";
        });

        
    </script>


<?php } ?>