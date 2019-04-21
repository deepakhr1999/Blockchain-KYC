#Reference: Hyperledger docs: https://hyperledger-fabric.readthedocs.io/en/release-1.4/build_network.html


head='\033[0;31m'
red='\033[0;31m'
green='\033[0;32'
close='\033[0m'
COMPOSE_FILE_COUCH=docker-compose-couch.yaml
COMPOSE_FILE=docker-compose-cli.yaml
#non docker commands

printf "${head}$(tput bold)+--------------------------------------------------------------+\n"
printf "|  These commands are to be executed in the terminal directly  |\n"
printf "+--------------------------------------------------------------+\n$(tput sgr0)${close}"


printf "${head}$(tput bold)+--------------------------------------------------------------+\n"
printf "|       Used crypto-config.yaml to generate certificates       |\n"
printf "+--------------------------------------------------------------+\n$(tput sgr0)${close}"
printf "These are the organizations' domains:\n"
../bin/cryptogen generate --config=./crypto-config.yaml


#make an orderer using a set profile: TwoOrgsOrdererGenesis
printf "${head}$(tput bold)+--------------------------------------------------------------+\n"
printf "|        Making a genesis block for a two orgs orderer:        |\n"
printf "+--------------------------------------------------------------+\n$(tput sgr0)${close}"
export FABRIC_CFG_PATH=$PWD
../bin/configtxgen -profile TwoOrgsOrdererGenesis -channelID byfn-sys-channel -outputBlock ./channel-artifacts/genesis.block


#channel configuration transaction
#default channel name is "mychannel"
printf "${head}$(tput bold)+--------------------------------------------------------------+\n"
printf "|          Create a Channel Configuration Transaction          |\n"
printf "+--------------------------------------------------------------+\n$(tput sgr0)${close}"
CHANNEL_NAME=mychannel
../bin/configtxgen -profile TwoOrgsChannel -outputCreateChannelTx ./channel-artifacts/channel.tx -channelID $CHANNEL_NAME
#Make the two orgs join the channel like so


printf "${head}$(tput bold)+--------------------------------------------------------------+\n"
printf "|                   Anchor Peer for each Org                   |\n"
printf "+--------------------------------------------------------------+\n$(tput sgr0)${close}"
../bin/configtxgen -profile TwoOrgsChannel -outputAnchorPeersUpdate ./channel-artifacts/Org1MSPanchors.tx -channelID $CHANNEL_NAME -asOrg Org1MSP
../bin/configtxgen -profile TwoOrgsChannel -outputAnchorPeersUpdate ./channel-artifacts/Org2MSPanchors.tx -channelID $CHANNEL_NAME -asOrg Org2MSP
../bin/configtxgen -profile TwoOrgsChannel -outputAnchorPeersUpdate ./channel-artifacts/Org3MSPanchors.tx -channelID $CHANNEL_NAME -asOrg Org3MSP
printf "${head}$(tput bold)----------------------------------------------------------------$(tput sgr0)${close}"
echo

#enter into docker terminal
docker-compose -f $COMPOSE_FILE -f $COMPOSE_FILE_COUCH up -d
docker exec cli scripts/make_peer.sh do
# docker exec -it cli bash