import { V10VmOpenApiElectronicChannel, V10VmOpenApiServiceLocationChannel, V10VmOpenApiWebPageChannel, V10VmOpenApiPrintableFormChannel, V10VmOpenApiPhoneChannel, V10VmOpenApiService } from "./model/v10/api";
import { wp } from "wp";

declare var wp: wp;
type ServiceChannel = V10VmOpenApiElectronicChannel | V10VmOpenApiPhoneChannel | V10VmOpenApiPrintableFormChannel | V10VmOpenApiServiceLocationChannel | V10VmOpenApiWebPageChannel;
type Service = V10VmOpenApiService;
const PTV_URL = "https://api.palvelutietovaranto.suomi.fi/api";
const PTV_VERSION = "v10";

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
   * @param id service channel id
   * @returns found service or null if not found
   */
  public findServiceChannel = async (id: string): Promise<ServiceChannel | null> => {
    const channels = await this.findServiceChannels([id]);
    return channels[0] || null;
  }

  /**
   * Finds a service by id
   * 
   * @param id service id
   * @returns found service or null if not found
   */
  public findService = async (id: string): Promise<Service | null> => {
    const channels = await this.findServices([id]);
    return channels[0] || null;
  }

  /**
   * Finds a service channels by ids
   * 
   * @param ids service channel ids
   * @returns found service or null if not found
   */
  public findServiceChannels = async (ids: string[]): Promise<ServiceChannel[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.channelCache.has(id));
    if (cacheMissIds.length > 0) {
      const result = await fetch(`${PTV_URL}/${PTV_VERSION}/ServiceChannel/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const channels = await result.json();
      
      channels.forEach((channel: ServiceChannel) => this.channelCache.set(channel.id, channel));
    }

    return ids.map(id => this.channelCache.get(id));
  }

  /**
   * Finds a services by ids
   * 
   * @param ids service id
   * @returns found service or null if not found
   */
  public findServices = async (ids: string[]): Promise<Service[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.channelCache.has(id));
    if (cacheMissIds.length > 0) {
      const result = await fetch(`${PTV_URL}/${PTV_VERSION}/Service/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const channels = await result.json();
      
      channels.forEach((channel: ServiceChannel) => this.channelCache.set(channel.id, channel));
    }

    return ids.map(id => this.channelCache.get(id));
  }

}