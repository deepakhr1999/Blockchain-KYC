#!/bin/bash
export CHANNEL_NAME=mychannel
ORDERER_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/tlsca.example.com-cert.pem
PEER0_ORG1_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org1.example.com/peers/peer0.org1.example.com/tls/ca.crt
PEER0_ORG2_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org2.example.com/peers/peer0.org2.example.com/tls/ca.crt
PEER0_ORG3_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org3.example.com/peers/peer0.org3.example.com/tls/ca.crt
ccpath=github.com/chaincode/private/go/
colconpath=github.com/chaincode/private/collections_config.json
func=$1

if [ -z $1 ]; then
	func=help
fi

function init(){
	PEER=$1
	ORG=$2
	CORE_PEER_MSPCONFIGPATH=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/users/Admin@org${ORG}.example.com/msp
	CORE_PEER_ADDRESS=peer${PEER}.org${ORG}.example.com:7051
	CORE_PEER_LOCALMSPID="Org${ORG}MSP"
	CORE_PEER_TLS_ROOTCERT_FILE=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/peers/peer${PEER}.org${ORG}.example.com/tls/ca.crt
}
#colors
blue='\033[0;34m'
red='\033[0;31m'
green='\033[0;32m'
close='\033[0m'
res=0

if [ $func = "do" ]; then
	./scripts/make_peer.sh make_channel
	
	for peer in 0 1; do
		for org in 1 2 3; do
			./scripts/make_peer.sh peer_join_channel $peer $org
		done
	done
	
	for org in 1 2 3; do
		./scripts/make_peer.sh update_anchor_peer 0 $org
	done

	for peer in 0 1; do
		for org in 1 2 3; do
			./scripts/make_peer.sh cc_install $peer $org
		done
	done

	./scripts/make_peer.sh cc_init

elif [ $func = "make_channel" ]; then
	printf "${blue}Making channel with Orderer's CA${close}\n"
	init 0 1
	peer channel create -o orderer.example.com:7050 -c $CHANNEL_NAME -f ./channel-artifacts/channel.tx --tls --cafile $ORDERER_CA
	res=$?

elif [ $func = "peer_join_channel" ]; then
	PEER=$2
	ORG=$3
	printf "${blue}Join peer to the channel: Peer: ${PEER}, Org: ${ORG}${close}\n"
	init $PEER $ORG
	peer channel join -b mychannel.block
	res=$?

elif [ $func = "update_anchor_peer" ]; then
	printf "${blue}Updating anchor PEER${close}\n"
	PEER=$2
	ORG=$3
	init $PEER $ORG
	peer channel update -o orderer.example.com:7050 -c $CHANNEL_NAME -f ./channel-artifacts/Org${ORG}MSPanchors.tx --tls --cafile $ORDERER_CA
	res=$?

elif [ $func = "cc_install" ]; then
	PEER=$2
	ORG=$3
	init $PEER $ORG
	printf "${blue}Installing chaincode on peer${PEER} on org${ORG} ${close}\n"
	peer chaincode install -n mycc -v 1.0 -p $ccpath
	res=$?

elif [ $func = "cc_init" ]; then
	printf "${blue}Initializing ledger on orderer with ${colconpath} private collection${close}\n"
	peer chaincode instantiate -o orderer.example.com:7050 --tls --cafile $ORDERER_CA -C $CHANNEL_NAME -n mycc -v 1.0 -c '{"Args":["init"]}' -P "OR ('Org1MSP.peer','Org2MSP.peer', 'Org3MSP.peer')" --collections-config  $GOPATH/src/$colconpath		
	res=$?
else 
	printf "${blue}Arg must be one of the following:${close}\n"
	printf "${blue}Help:		${close}\n"
	printf "${green}  ## Command            |  Arguments         ${close}\n"
	printf "  1) make_channel       |                    \n"
	printf "  2) peer_join_channel  |  <peer_no> <org_no>\n"
	printf "  3) update_anchor_peer |  <peer_no> <org_no>\n"
	printf "  4) cc_install		|  <peer_no> <org_no>\n"
	printf "  5) cc_init		|  <peer_no> <org_no>\n"
	printf "  6) do 		|					 \n"
	func=help
fi

if [ $res -ne 0 ]; then
	FILE=${0##*/}
	printf "${red}ERROR: $FILE, could not execute $func${close}\n"
else
	printf "${green}SUCCESS: $FILE executed $func${close}\n\n"
fi