$(function () {

  $.ajax({
    dataType: 'json',
    url: '/_twitter/timeline',
    type: 'get',
    data: {},
    success: function (data) {
      $.each(data, function(index, tweet) {
        var html = '<article>';
        html += '<a href="//twitter.com/'+tweet.user.screen_name+'">';
        html += '<img src="'+tweet.user.profile_image_url_https+'" />';
        html += '<span class="user-name">' + tweet.user.name + '</span><br/>';
        html += '<span class="user-screen-name">@' + tweet.user.screen_name + '</span>';
        html += '</a>';
        html += '<a class="ago" href="https://twitter.com/'+ tweet.user.screen_name + '/status/' + tweet.id_str + '">' + tweet.time_ago + '</a>';
        html += '<p>' + tweet.text + '</p>';
        html += '</article>';
        $('.twitter-feed').append(html);
      });
    },
    error: function (data) {
      $('.twitter-feed').append('<p>Twitter feed unavailable</p>');
    }
  });

});
