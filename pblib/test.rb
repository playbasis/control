require_relative 'playbasis'
require 'test/unit'
include Test::Unit::Assertions

pb = Playbasis.new()

puts 'auth'
result = pb.auth('abc','abcde')
pp result

id = '1'

#puts 'register'
#user = "#{Time.now.to_i}"
#result = pb.register('id'+user, 'user'+user, user+'@email.com', user+'.jpg', {:first_name => 'fname', :last_name => 'lname'})
#pp result

puts "\nlogin"
result = pb.login(id)
assert(result['success'])
pp result

puts "\nlogout"
result = pb.logout(id)
assert(result['success'])
pp result

puts "\npoints"
result = pb.points(id)
assert(result['success'])
pp result

puts "\npoint"
result = pb.point(id, 'exp')
assert(result['success'])
pp result

puts "\nactionLastPerformed"
result = pb.actionLastPerformed(id)
assert(result['success'])
pp result

puts "\nactionLastPerformedTime"
result = pb.actionLastPerformedTime(id, 'like')
assert(result['success'])
pp result

puts "\nactionPerformedCount"
result = pb.actionPerformedCount(id, 'like')
assert(result['success'])
pp result

puts "\nbadgeOwned"
result = pb.badgeOwned(id)
assert(result['success'])
pp result

puts "\nrank"
result = pb.rank('exp', 10)
assert(result['success'])
pp result

puts "\nplayer"
result = pb.player(id)
assert(result['success'])
pp result

puts "\nbadges"
result = pb.badges()
assert(result['success'])
pp result

puts "\nbadge"
result = pb.badge(2)
assert(result['success'])
pp result

puts "\nbadgeCollections"
result = pb.badgeCollections()
assert(result['success'])
pp result

puts "\nbadgeCollection"
result = pb.badgeCollection(1)
assert(result['success'])
pp result

puts "\nactionConfig"
result = pb.actionConfig()
assert(result['success'])
pp result

puts "\nrule"
result = pb.rule(id, 'like')
assert(result['success'])
pp result

puts "\ndone\n"