import config from "../config";
import { V10VmOpenApiElectronicChannel, V10VmOpenApiServiceLocationChannel, V10VmOpenApiWebPageChannel, V10VmOpenApiPrintableFormChannel, V10VmOpenApiPhoneChannel } from "./model/v10/api";
import { wp } from "wp";

declare var wp: wp;
type ServiceChannel = V10VmOpenApiElectronicChannel | V10VmOpenApiPhoneChannel | V10VmOpenApiPrintableFormChannel | V10VmOpenApiServiceLocationChannel | V10VmOpenApiWebPageChannel;
/**
 * PTV client
 */
export default class PTV {

  private channelCache: Map<string, ServiceChannel>;

  constructor() {
    this.channelCache = new Map();
  }

  /**
   * Finds a service channel by id
   * 
   * @param id id
   * @returns found service or null if not found
   */
  public findServiceChannel = async (id: string): Promise<ServiceChannel | null> => {
    const channels = await this.findServiceChannels([id]);
    return channels[0] || null;
  }

  /**
   * Finds a service channel by id
   * 
   * @param id id
   * @returns found service or null if not found
   */
  public findServiceChannels = async (ids: string[]): Promise<ServiceChannel[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.channelCache.has(id));
    if (cacheMissIds.length > 0) {
      const result = await fetch(`${config.ptv.url}/${config.ptv.version}/ServiceChannel/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const channels = await result.json();
      
      channels.forEach((channel: ServiceChannel) => this.channelCache.set(channel.id, channel));
    }

    return ids.map(id => this.channelCache.get(id));
  }

}